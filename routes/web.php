<?php

use App\Livewire\Personas\Index as PersonasIndex;
use App\Livewire\Personas\Create as PersonasCreate;
use App\Livewire\Personas\Edit as PersonasEdit;
use App\Livewire\Personas\Show as PersonasShow;
use App\Livewire\Personas\MiPerfil;
use App\Livewire\Estudiantes\Index as EstudiantesIndex;

use App\Http\Controllers\Directorio\DirectorioExportController;
use App\Http\Controllers\Directorio\DirectorioPublicFeedController;
use App\Livewire\Directorio\Index as DirectorioIndex;

use App\Livewire\Sys\Catalogos\Index as CatalogosIndex;
use App\Livewire\Sys\Config\AuthSettings;
use App\Livewire\Sys\Documentacion\Index as DocumentacionIndex;
use App\Livewire\Sys\PerfilesPublicos\Index as PerfilesPublicosIndex;
use App\Livewire\Sys\PermissionsMatrix;
use App\Livewire\Sys\RolesPermissions\Index as RolesPermissionsIndex;
use App\Livewire\Sys\Users\Index as UsersIndex;
use App\Livewire\Sys\Users\Security;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', '2fa.configured', 'identity.resolve', 'permission:dashboard.view'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', '2fa.configured', 'identity.resolve'])
    ->prefix('personas')
    ->name('personas.')
    ->group(function () {
        Route::get('/', PersonasIndex::class)
            ->middleware(['permission:personas.view'])
            ->name('index');
        Route::get('/crear', PersonasCreate::class)
            ->middleware(['permission:personas.create'])
            ->name('create');
        Route::get('/{persona}', PersonasShow::class)
            ->middleware(['permission:personas.view'])
            ->name('show');
        Route::get('/{persona}/editar', PersonasEdit::class)
            ->middleware(['permission:personas.update'])
            ->name('edit');
        //Route::get('/mi-perfil', MiPerfil::class)
        //    ->name('mi-perfil')
        //    ->middleware('identity.valid');
    });

Route::middleware(['auth', 'verified', '2fa.configured', 'identity.resolve'])
    ->prefix('estudiantes')
    ->name('estudiantes.')
    ->group(function () {
        Route::middleware('permission:estudiantes.view')
            ->get('/', EstudiantesIndex::class)
            ->name('index');
    });

Route::middleware(['auth', 'verified', '2fa.configured', 'identity.resolve'])
    ->prefix('sys')
    ->name('sys.')
    ->group(function () {
        Route::get('/users', UsersIndex::class)
            ->middleware('permission:users.view')
            ->name('users.index');
        Route::get('/roles-permisos', RolesPermissionsIndex::class)
            ->middleware(['auth', 'role:super-admin'])
            ->name('roles-permisos.index');
        Route::get('/documentacion', DocumentacionIndex::class)
            ->name('documentacion.index');
        Route::get('/matriz-permisos', PermissionsMatrix::class)
            ->middleware('permission:matrix.view')
            ->name('permissions-matrix');
        Route::get('/catalogos', CatalogosIndex::class)
            ->middleware('permission:catalogos.view')
            ->name('catalogos');
        Route::get('/configuracion', AuthSettings::class)
            ->middleware('permission:sys.settings')
            ->name('settings');
        Route::get('/perfiles-publicos', PerfilesPublicosIndex::class)
            ->name('perfiles-publicos.index');
    });

Route::middleware(['auth', 'identity.resolve'])
    ->prefix('directorio')
    ->name('directorio.')
    ->group(function () {
        Route::get('/', DirectorioIndex::class)
            ->name('index')
            ->middleware('permission:directorio.view');

        Route::get('/export/{format}', DirectorioExportController::class)
            ->name('export')
            ->whereIn('format', ['csv', 'xlsx', 'json'])
            ->middleware('can:directorio.export');
    });

Route::middleware(['auth', 'verified'])
    ->get('/user/security', Security::class)
    ->name('user.security');

Route::prefix('api')
    ->name('api.')
    ->group(function () {
        Route::get('/public/directorio.{format}', DirectorioPublicFeedController::class)
            ->whereIn('format', ['json', 'csv'])
            ->name('public.directorio.feed');
    });

Route::get('/test-identity', function () {
    return [
        'current_identity_id' => currentIdentityId(),
        'current_identity_type' => currentIdentityType(),
        'current_identity' => currentIdentity()?->toArray(),
        'profile_fullname' => currentProfileFullname(),
        'profile_email' => currentProfileEmail(),
        'profile_initials' => currentProfileInitials(),
        'profile' => currentProfile(),
        'is_siiap_student' => currentIdentityIsSiiapStudent(),
        'is_siiaa' => currentIdentityIsSiiaa(),
        'is_impersonating' => isImpersonatingIdentity(),
    ];
})->middleware(['auth', 'identity.resolve']);

require __DIR__ . '/settings.php';