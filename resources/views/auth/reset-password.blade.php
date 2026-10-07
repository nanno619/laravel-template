<x-auth.shell title="Atur ulang kata sandi" heading="Atur ulang kata sandi" description="Buat kata sandi baru untuk akun Anda.">
  <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <x-ui.field label="Email" name="email">
      <x-ui.input type="email" name="email" :value="$request->email" autocomplete="email" required />
    </x-ui.field>
    <x-ui.field label="Kata sandi baru" name="password">
      <x-ui.input type="password" name="password" autocomplete="new-password" autofocus required />
    </x-ui.field>
    <x-ui.field label="Konfirmasi kata sandi" name="password_confirmation">
      <x-ui.input type="password" name="password_confirmation" autocomplete="new-password" required />
    </x-ui.field>
    <button type="submit" class="btn btn-primary w-full">Atur ulang kata sandi</button>
  </form>
</x-auth.shell>
