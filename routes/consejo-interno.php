<?php

/*
 * use App\Livewire\ConsejoInterno\Index as ConsejoInternoIndex;
 *
 * use App\Livewire\ConsejoInterno\Actas\Show as ActasShow;
 * use App\Livewire\Actas\Index as ActasPublicIndex;
 * use App\Livewire\Actas\Show as ActasPublicShow;
 * use App\Http\Controllers\ConsejoInterno\ActaPrintController;
 */

use App\Livewire\ConsejoInterno\Actas\Edit as ActasEdit;
use App\Livewire\ConsejoInterno\Actas\Index as ActasIndex;
use App\Livewire\ConsejoInterno\Reuniones\Edit as ReunionesEdit;
use App\Livewire\ConsejoInterno\Reuniones\Index as ReunionesIndex;
use App\Livewire\ConsejoInterno\Reuniones\Show as ReunionesShow;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | Consejo Interno
 * |--------------------------------------------------------------------------
 * |
 * | Rutas operativas del módulo Consejo Interno.
 * |
 * | Pendiente: activar las rutas conforme se creen los componentes Livewire
 * | correspondientes. No se importan clases inexistentes para evitar romper
 * | route:list durante la implementación por bloques.
 * |
 */

Route::middleware(['auth', 'verified', '2fa.configured', 'identity.resolve'])
    ->prefix('consejo-interno')
    ->name('consejo-interno.')
    ->group(function () {
        Route::get('/reuniones', ReunionesIndex::class)
            ->middleware('permission:ci.reuniones_view')
            ->name('reuniones.index');

        Route::get('/reuniones/crear', ReunionesEdit::class)
            ->middleware('permission:ci.reuniones_manage')
            ->name('reuniones.create');

        Route::get('/reuniones/{reunion}/editar', ReunionesEdit::class)
            ->middleware('can:update,reunion')
            ->name('reuniones.edit');

        Route::get('/reuniones/{reunion}', ReunionesShow::class)
            ->middleware('can:view,reunion')
            ->name('reuniones.show');

        // Actas
        Route::get('/actas', ActasIndex::class)
            ->name('actas.index');

        Route::get('/actas/crear', ActasEdit::class)
            ->middleware('permission:ci.actas_manage')
            ->name('actas.create');

        Route::get('/actas/{acta}/editar', ActasEdit::class)
            ->middleware('can:update,acta')
            ->name('actas.edit');
    });

/*
 * Route::middleware(['auth', 'verified', '2fa.configured', 'identity.resolve'])
 *     ->prefix('consejo-interno')
 *     ->name('consejo-interno.')
 *     ->group(function () {
 *         Route::get('/', ConsejoInternoIndex::class)
 *             ->middleware('permission:ci.reuniones_view')
 *             ->name('index');
 *
 *         Route::get('/reuniones', ReunionesIndex::class)
 *             ->middleware('permission:ci.reuniones_view')
 *             ->name('reuniones.index');
 *
 *         Route::get('/reuniones/crear', ReunionesEdit::class)
 *             ->middleware('permission:ci.reuniones_manage')
 *             ->name('reuniones.create');
 *
 *         Route::get('/reuniones/{reunion}', ReunionesShow::class)
 *             ->middleware('can:view,reunion')
 *             ->name('reuniones.show');
 *
 *         Route::get('/reuniones/{reunion}/editar', ReunionesEdit::class)
 *             ->middleware('can:update,reunion')
 *             ->name('reuniones.edit');
 *
 *         Route::get('/actas', ActasIndex::class)
 *             ->middleware('permission:ci.actas_manage')
 *             ->name('actas.index');
 *
 *         Route::get('/actas/crear', ActasEdit::class)
 *             ->middleware('permission:ci.actas_manage')
 *             ->name('actas.create');
 *
 *         Route::get('/actas/{acta}', ActasShow::class)
 *             ->middleware('can:view,acta')
 *             ->name('actas.show');
 *
 *         Route::get('/actas/{acta}/editar', ActasEdit::class)
 *             ->middleware('can:update,acta')
 *             ->name('actas.edit');
 *     });
 */

/*
 * |--------------------------------------------------------------------------
 * | Actas institucionales publicadas
 * |--------------------------------------------------------------------------
 * |
 * | Consulta institucional independiente del módulo operativo de Consejo Interno.
 * | Solo muestra actas publicadas y no expone evaluaciones, comentarios ni datos
 * | internos.
 * |
 */

/*
 * Route::middleware(['auth', 'verified', '2fa.configured', 'identity.resolve'])
 *     ->prefix('actas')
 *     ->name('actas.')
 *     ->group(function () {
 *         Route::get('/', ActasPublicIndex::class)
 *             ->middleware('permission:ci.actas_view')
 *             ->name('index');
 *
 *         Route::get('/{acta}', ActasPublicShow::class)
 *             ->middleware('permission:ci.actas_view')
 *             ->name('show');
 *
 *         Route::get('/{acta}/imprimir', ActaPrintController::class)
 *             ->middleware('permission:ci.actas_view')
 *             ->name('print');
 *     });
 */
