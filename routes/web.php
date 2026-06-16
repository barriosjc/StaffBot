<?php

use App\Livewire\Usuarios\Form as UsuariosForm;
use App\Livewire\Usuarios\Index as UsuariosIndex;
use App\Livewire\Horarios\Form as HorariosForm;
use App\Livewire\Horarios\Index as HorariosIndex;
use App\Livewire\SolicitudCoberturas\Index as SolicitudesCoberturaIndex;
use App\Livewire\SolicitudCoberturas\Form as SolicitudesCoberturaForm;
use Illuminate\Http\Request;
use App\Http\Controllers\HorarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'layouts.app')->name('main');

Route::middleware('auth')->prefix('usuarios')->name('usuarios.')->group(function () {
    Route::livewire('/', UsuariosIndex::class)->name('index');
    Route::livewire('/create', UsuariosForm::class)->name('create');
    Route::livewire('/nuevo', UsuariosForm::class)->name('create.nuevo');
    Route::livewire('/{usuario}/edit', UsuariosForm::class)->name('edit');
});

Route::middleware('auth')->prefix('horarios')->name('horarios.')->group(function () {
    Route::livewire('/', HorariosIndex::class)->name('index');
    Route::get('/create', [HorarioController::class, 'create'])->name('create');
    Route::get('/nuevo', [HorarioController::class, 'create'])->name('create.nuevo');
    Route::post('/', [HorarioController::class, 'store'])->name('store');
    Route::livewire('/{horario}/edit', HorariosForm::class)->name('edit');
});

Route::middleware('auth')->prefix('solicitud-coberturas')->name('solicitud-coberturas.')->group(function () {
    Route::livewire('/', SolicitudesCoberturaIndex::class)->name('index');
    Route::livewire('/creare/{solicitudId?}', SolicitudesCoberturaForm::class)->name('create');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::post('/whatsapp/webhook', App\Http\Controllers\WhatsAppWebhookController::class)->name('whatsapp.webhook');

// Rutas de autenticación básicas para evitar errores si las llamadas existen
Route::view('/login', 'auth.login')->name('login');
Route::get('restablecer', function () {
    return 'Restablecer contraseña - formulario de ejemplo';
})->name('login.restablecer');
