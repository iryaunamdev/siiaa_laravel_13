<?php

namespace App\Services\Auth;

use Exception;
use App\Services\System\SettingService;

class LdapService
{
    protected array $config;

    protected SettingService $settings;

    public function __construct()
    {
        $connection = config('ldap_auth.connection', 'default');

        $this->config = config("ldap_auth.connections.{$connection}", []);

        $this->settings = app(SettingService::class);
    }
    public function isEnabled(): bool
    {
        return (bool) $this->settings->get(
            'auth.ldap.enabled',
            config('ldap_auth.enabled', false)
        );
    }

    public function testConnection(): bool
    {
        $connection = $this->connect();

        if (! $connection) {
            return false;
        }

        $bound = @ldap_bind(
            $connection,
            $this->config['username'] ?? null,
            $this->config['password'] ?? null
        );

        ldap_unbind($connection);

        return $bound;
    }

    protected function connect()
    {
        $host = $this->config['host'] ?? null;
        $port = (int) ($this->config['port'] ?? 389);
        $timeout = (int) ($this->config['timeout'] ?? 5);

        if (! $host) {
            return false;
        }

        $connection = @ldap_connect($host, $port);

        if (! $connection) {
            return false;
        }

        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, $timeout);

        if (($this->config['use_tls'] ?? false) === true) {
            @ldap_start_tls($connection);
        }

        return $connection;
    }

    public function findUser(string $username): ?array
    {
        $connection = $this->connect();

        if (! $connection) {
            return null;
        }

        // Bind con usuario de servicio
        $bind = @ldap_bind(
            $connection,
            $this->config['username'] ?? null,
            $this->config['password'] ?? null
        );

        if (! $bind) {
            ldap_unbind($connection);
            return null;
        }

        $baseDn = $this->config['base_dn'] ?? '';
        $attribute = config('ldap_auth.attributes.username', 'uid');

        $filter = sprintf('(%s=%s)', $attribute, $username);

        $search = @ldap_search($connection, $baseDn, $filter);

        if (! $search) {
            ldap_unbind($connection);
            return null;
        }

        $entries = ldap_get_entries($connection, $search);

        ldap_unbind($connection);

        if ($entries['count'] === 0) {
            return null;
        }

        return $entries[0];
    }

    public function authenticate(string $username, string $password): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        if (blank($username) || blank($password)) {
            return null;
        }

        $user = $this->findUser($username);

        if (! $user || empty($user['dn'])) {
            return null;
        }

        $connection = $this->connect();

        if (! $connection) {
            return null;
        }

        $authenticated = @ldap_bind(
            $connection,
            $user['dn'],
            $password
        );

        ldap_unbind($connection);

        if (! $authenticated) {
            return null;
        }

        return $this->normalizeUser($user);
    }

    protected function normalizeUser(array $ldapUser): array
    {
        return [
            'dn' => $ldapUser['dn'] ?? null,
            'username' => $ldapUser['uid'][0] ?? null,
            'name' => $ldapUser['cn'][0]
                ?? trim(($ldapUser['givenname'][0] ?? '') . ' ' . ($ldapUser['sn'][0] ?? '')),
            'email' => $ldapUser['mail'][0] ?? null,
            'given_name' => $ldapUser['givenname'][0] ?? null,
            'surname' => $ldapUser['sn'][0] ?? null,
        ];
    }

    public function isLoggingEnabled(): bool
    {
        return (bool) $this->settings->get(
            'auth.ldap.logging',
            config('ldap_auth.logging', false)
        );
    }
}