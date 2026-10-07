<x-layouts.admin title="Pengaturan akun" group="Halaman">
@php $securityTab = $errors->updatePassword->any() || session('status') === 'password-updated'; @endphp
<x-ui.page-header title="Pengaturan akun" description="Kelola profil, keamanan, dan preferensi notifikasi." />

<div data-tabs>
  <div class="tabs-underline" role="tablist" aria-label="Pengaturan">
    <button type="button" role="tab" class="tab" id="st-1" aria-selected="{{ $securityTab ? 'false' : 'true' }}" @if ($securityTab) tabindex="-1" @endif aria-controls="sp-1"><x-ui.icon name="user" />Profil</button>
    <button type="button" role="tab" class="tab" id="st-2" aria-selected="{{ $securityTab ? 'true' : 'false' }}" @unless ($securityTab) tabindex="-1" @endunless aria-controls="sp-2"><x-ui.icon name="lock" />Keamanan</button>
    <button type="button" role="tab" class="tab" id="st-3" aria-selected="false" tabindex="-1" aria-controls="sp-3"><x-ui.icon name="bell" />Notifikasi</button>
  </div>

  <!-- Profil -->
  <div id="sp-1" role="tabpanel" aria-labelledby="st-1" class="grid gap-4 pt-6 lg:grid-cols-3" @if ($securityTab) hidden @endif>
    <div class="card h-fit p-6 text-center">
      <span class="avatar avatar-xl mx-auto">{{ auth()->user()->initials }}</span>
      <h2 class="mt-3 font-semibold">{{ auth()->user()->name }}</h2>
      <p class="text-sm text-muted-foreground">{{ auth()->user()->email }}</p>
      <div class="mt-3 flex justify-center gap-2"><span class="badge badge-success badge-dot">Aktif</span><span class="badge badge-soft">Admin</span></div>
      <button type="button" class="btn btn-outline mt-5 w-full"><x-ui.icon name="upload" />Ganti foto</button>
    </div>
    <form method="POST" action="{{ route('user-profile-information.update') }}" class="card lg:col-span-2">
      @csrf
      @method('PUT')
      <div class="card-header"><h2 class="card-title">Informasi profil</h2><p class="card-desc">Perbarui nama dan email Anda.</p></div>
      <div class="card-content grid gap-4 sm:grid-cols-2">
        @if (session('status') === 'profile-information-updated')
          <x-ui.alert variant="success" class="sm:col-span-2">Profil berhasil diperbarui.</x-ui.alert>
        @endif
        <x-ui.field label="Nama" name="name" class="sm:col-span-2">
          <x-ui.input name="name" :value="auth()->user()->name" autocomplete="name" required :invalid="$errors->updateProfileInformation->has('name')" />
          @if ($errors->updateProfileInformation->has('name')) <p class="field-error">{{ $errors->updateProfileInformation->first('name') }}</p> @endif
        </x-ui.field>
        <x-ui.field label="Email" name="email" class="sm:col-span-2">
          <x-ui.input type="email" name="email" :value="auth()->user()->email" autocomplete="email" required :invalid="$errors->updateProfileInformation->has('email')" />
          @if ($errors->updateProfileInformation->has('email')) <p class="field-error">{{ $errors->updateProfileInformation->first('email') }}</p> @endif
        </x-ui.field>
      </div>
      <div class="card-footer justify-end"><button type="submit" class="btn btn-primary">Simpan perubahan</button></div>
    </form>
  </div>

  <!-- Keamanan -->
  <div id="sp-2" role="tabpanel" aria-labelledby="st-2" class="space-y-4 pt-6" @unless ($securityTab) hidden @endunless>
    <form method="POST" action="{{ route('user-password.update') }}" class="card">
      @csrf
      @method('PUT')
      <div class="card-header"><h2 class="card-title">Ubah kata sandi</h2><p class="card-desc">Gunakan minimal 8 karakter.</p></div>
      <div class="card-content grid max-w-xl gap-4">
        @if (session('status') === 'password-updated')
          <x-ui.alert variant="success">Kata sandi berhasil diperbarui.</x-ui.alert>
        @endif
        <x-ui.field label="Kata sandi saat ini" name="current_password">
          <x-ui.input type="password" name="current_password" autocomplete="current-password" required :invalid="$errors->updatePassword->has('current_password')" />
          @if ($errors->updatePassword->has('current_password')) <p class="field-error">{{ $errors->updatePassword->first('current_password') }}</p> @endif
        </x-ui.field>
        <x-ui.field label="Kata sandi baru" name="password">
          <x-ui.input type="password" name="password" autocomplete="new-password" required :invalid="$errors->updatePassword->has('password')" />
          @if ($errors->updatePassword->has('password')) <p class="field-error">{{ $errors->updatePassword->first('password') }}</p> @endif
        </x-ui.field>
        <x-ui.field label="Konfirmasi kata sandi" name="password_confirmation">
          <x-ui.input type="password" name="password_confirmation" autocomplete="new-password" required :invalid="$errors->updatePassword->has('password_confirmation')" />
          @if ($errors->updatePassword->has('password_confirmation')) <p class="field-error">{{ $errors->updatePassword->first('password_confirmation') }}</p> @endif
        </x-ui.field>
      </div>
      <div class="card-footer justify-end"><button type="submit" class="btn btn-primary">Perbarui</button></div>
    </form>
    <div class="card p-6">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-4"><span class="avatar avatar-success avatar-lg"><x-ui.icon name="shield" class="h-5 w-5" /></span><div><h2 class="font-semibold">Verifikasi dua langkah</h2><p class="text-sm text-muted-foreground">Tambahkan lapisan keamanan dengan aplikasi autentikator.</p></div></div>
        <label class="switch"><input type="checkbox" class="peer sr-only" checked aria-label="Verifikasi dua langkah"><span class="switch-track"></span></label>
      </div>
    </div>
    <div class="rounded-xl border border-danger/30 bg-danger-soft p-6">
      <h2 class="font-semibold text-danger">Zona berbahaya</h2>
      <p class="mt-1 text-sm text-muted-foreground">Menghapus akun akan menghilangkan seluruh data secara permanen.</p>
      <button type="button" class="btn btn-destructive mt-4">Hapus akun</button>
    </div>
  </div>

  <!-- Notifikasi -->
  <div id="sp-3" role="tabpanel" aria-labelledby="st-3" class="pt-6" hidden>
    <div class="card">
      <div class="card-header"><h2 class="card-title">Preferensi notifikasi</h2><p class="card-desc">Pilih kabar yang ingin Anda terima.</p></div>
      <ul class="divide-y border-t">
        <li class="flex items-center justify-between gap-4 px-6 py-4"><div><p class="font-medium">Pesanan baru</p><p class="text-sm text-muted-foreground">Kabari saya setiap ada pesanan masuk.</p></div><label class="switch"><input type="checkbox" class="peer sr-only" checked aria-label="Pesanan baru"><span class="switch-track"></span></label></li>
        <li class="flex items-center justify-between gap-4 px-6 py-4"><div><p class="font-medium">Stok menipis</p><p class="text-sm text-muted-foreground">Peringatan saat stok di bawah batas minimum.</p></div><label class="switch"><input type="checkbox" class="peer sr-only" checked aria-label="Stok menipis"><span class="switch-track"></span></label></li>
        <li class="flex items-center justify-between gap-4 px-6 py-4"><div><p class="font-medium">Ringkasan mingguan</p><p class="text-sm text-muted-foreground">Laporan performa dikirim tiap Senin pagi.</p></div><label class="switch"><input type="checkbox" class="peer sr-only" aria-label="Ringkasan mingguan"><span class="switch-track"></span></label></li>
        <li class="flex items-center justify-between gap-4 px-6 py-4"><div><p class="font-medium">Promo dan berita</p><p class="text-sm text-muted-foreground">Info fitur baru dan penawaran.</p></div><label class="switch"><input type="checkbox" class="peer sr-only" aria-label="Promo dan berita"><span class="switch-track"></span></label></li>
      </ul>
    </div>
  </div>
</div>
</x-layouts.admin>
