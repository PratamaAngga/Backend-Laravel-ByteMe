@component('mail::message')
# Halo, {{ $username }}! 🎉

Your payment for the product **{{ $namaProduk }}** has been successfully confirmed.

Here is the link to your product:

@component('mail::button', ['url' => $linkAkses])
Access the Product Now
@endcomponent

**Detail Pesanan:**
- Order Number: `{{ $pesananId }}`
- Product: {{ $namaProduk }}

> ⚠️ Don't share this link with others as it is your private access link.

Thank you for shopping at **ByteMe Digital Marketplace**!

Sincerely,
ByteMe Team
@endcomponent