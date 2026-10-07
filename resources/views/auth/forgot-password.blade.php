<x-auth.shell title="Lupa kata sandi" heading="Lupa kata sandi?" description="Masukkan email akun Anda dan kami akan mengirim tautan untuk mengatur ulang kata sandi.">
  <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
    @csrf
    <x-ui.field label="Email" name="email">
      <x-ui.input type="email" name="email" placeholder="ayu@kopikenanga.id" autocomplete="email" autofocus required />
    </x-ui.field>
    <button type="submit" class="btn btn-primary w-full">Kirim tautan atur ulang</button>
  </form>
  <p class="mt-8 text-center text-sm text-muted-foreground"><a href="{{ route('login') }}" class="font-medium text-primary hover:underline">Kembali ke halaman masuk</a></p>
</x-auth.shell>
