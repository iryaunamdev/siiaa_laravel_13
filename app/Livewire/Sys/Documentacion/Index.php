<?php

namespace App\Livewire\Sys\Documentacion;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public array $documents = [];

    public ?array $selectedDocument = null;

    public string $content = '';
    public string $search = '';

    public ?string $lastOpenedSlug = null;
    public array $toc = [];

    public function mount(): void
    {
        $this->documents = $this->loadAllowedDocuments();

        $last = session('siiaa_docs_last');

        if ($last) {
            $this->selectDocument($last);
            return;
        }

        if (! empty($this->documents)) {
            $this->selectDocument($this->documents[0]['slug']);
        }
    }

    public function selectDocument(string $slug): void
    {
        session(['siiaa_docs_last' => $slug]);

        $document = collect($this->documents)
            ->firstWhere('slug', $slug);

        if (! $document) {
            abort(403, 'No tienes acceso a este documento.');
        }

        $path = 'siiaa/documentacion/' . $document['path'];

        if (! Storage::disk('local')->exists($path)) {
            $this->selectedDocument = $document;
            $this->content = '# Documento no encontrado';

            return;
        }

        $this->selectedDocument = $document;
        $this->lastOpenedSlug = $slug;
        $this->toc = $this->generateToc($this->content);
        $this->content = Storage::disk('local')->get($path);
    }

    private function loadAllowedDocuments(): array
    {
        $indexPath = 'siiaa/documentacion/index.json';

        if (! Storage::disk('local')->exists($indexPath)) {
            return [];
        }

        $documents = json_decode(
            Storage::disk('local')->get($indexPath),
            true
        );

        if (! is_array($documents)) {
            return [];
        }

        return collect($documents)
            ->filter(fn(array $document) => $this->userCanAccess($document))
            ->values()
            ->all();
    }

    private function userCanAccess(array $document): bool
    {
        $permissions = $document['permissions'] ?? [];

        if (empty($permissions)) {
            return true;
        }

        $user = Auth::user();

        if (! $user) {
            return false;
        }

        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    public function getHtmlContentProperty(): string
    {
        $html = \Illuminate\Support\Str::markdown($this->content);

        // Agregar anchors a h2 y h3
        $html = preg_replace_callback('/<(h2|h3)>(.*?)<\/\1>/', function ($matches) {
            $text = strip_tags($matches[2]);
            $id = str($text)->slug();

            return "<{$matches[1]} id=\"{$id}\">{$matches[2]}</{$matches[1]}>";
        }, $html);

        return $html;
    }

    public function getFilteredDocumentsProperty(): array
    {
        if (blank($this->search)) {
            return $this->documents;
        }

        return collect($this->documents)
            ->filter(function (array $document) {
                return str($document['title'] ?? '')->lower()->contains(str($this->search)->lower())
                    || str($document['category'] ?? '')->lower()->contains(str($this->search)->lower());
            })
            ->values()
            ->all();
    }

    private function filteredDocuments(): array
    {
        if (blank($this->search)) {
            return $this->documents;
        }

        return collect($this->documents)
            ->filter(function (array $document) {
                $search = str($this->search)->lower();

                return str($document['title'] ?? '')->lower()->contains($search)
                    || str($document['category'] ?? '')->lower()->contains($search);
            })
            ->values()
            ->all();
    }

    private function groupedDocuments(): array
    {
        return collect($this->filteredDocuments())
            ->groupBy(fn(array $document) => $document['category'] ?? 'General')
            ->toArray();
    }

    private function generateToc(string $content): array
    {
        preg_match_all('/^(##|###)\s+(.*)$/m', $content, $matches, PREG_SET_ORDER);

        return collect($matches)->map(function ($match) {
            $level = $match[1] === '##' ? 2 : 3;
            $text = trim($match[2]);
            $anchor = str($text)->slug();

            return [
                'level' => $level,
                'text' => $text,
                'anchor' => $anchor,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.sys.documentacion.index', [
            'groupedDocuments' => $this->groupedDocuments(),
        ]);
    }
}