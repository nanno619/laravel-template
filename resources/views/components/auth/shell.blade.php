@props(['title', 'heading', 'description' => null])
<x-layouts.guest :title="$title">
<div class="grid min-h-screen lg:grid-cols-2">
  <div class="relative flex flex-col justify-center px-6 py-12 sm:px-12">
    <button type="button" data-theme-toggle aria-label="Ganti tema" class="btn btn-outline btn-icon absolute right-4 top-4"><svg class="h-4 w-4" aria-hidden="true"><use class="js-theme-icon" href="{{ asset('icons.svg') }}#i-moon"/></svg></button>
    <div class="mx-auto w-full max-w-sm">
      <div class="flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-md bg-primary text-primary-foreground"><x-ui.icon name="coffee" class="h-5 w-5" /></span>
        <span class="text-lg font-semibold">{{ config('kenanga.brand.workspace') }}</span>
      </div>
      <h1 class="mt-8 text-2xl font-semibold tracking-tight">{{ $heading }}</h1>
      @if ($description)
        <p class="mt-1 text-muted-foreground">{{ $description }}</p>
      @endif

      @if (session('status'))
        <x-ui.alert variant="success" class="mt-6">{{ session('status') }}</x-ui.alert>
      @endif

      {{ $slot }}
    </div>
  </div>

  <div class="hidden flex-col justify-between bg-primary p-12 text-primary-foreground lg:flex">
    <div class="flex items-center gap-2 text-sm font-medium opacity-90"><x-ui.icon name="layers" />{{ config('kenanga.brand.name') }}</div>
    <figure>
      <blockquote class="text-2xl font-medium leading-snug">“Semua yang saya butuhkan untuk mengelola toko ada di satu tempat, rapi dan cepat.”</blockquote>
      <figcaption class="mt-4 text-sm opacity-80">Hendra Wijaya · Pemilik Kopi Kenanga</figcaption>
    </figure>
  </div>
</div>
</x-layouts.guest>
