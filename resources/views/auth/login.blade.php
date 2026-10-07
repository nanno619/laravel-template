<x-auth.shell title="Masuk" heading="Masuk ke akun Anda" description="Masukkan email dan kata sandi untuk melanjutkan.">
  <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
    @csrf
    <x-ui.field label="Email" name="email">
      <x-ui.input type="email" name="email" placeholder="ayu@kopikenanga.id" autocomplete="email" autofocus required />
    </x-ui.field>
    <x-ui.field name="password">
      <div class="flex items-center justify-between">
        <label class="label" for="f-password">Kata sandi</label>
        <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">Lupa kata sandi?</a>
      </div>
      <x-ui.input type="password" name="password" placeholder="••••••••" autocomplete="current-password" required />
    </x-ui.field>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Ingat saya di perangkat ini</label>
    <button type="submit" class="btn btn-primary w-full">Masuk</button>
  </form>
</x-auth.shell>
