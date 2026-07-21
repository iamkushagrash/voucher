@extends('layouts.app')

@section('title', 'Reseller Storefront | AeronPay B2B Gift Cards')

@section('content')
<div class="space-y-8 animate-fade-in">

    <!-- Top Reseller Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-panel p-6 rounded-2xl border border-cyan-500/20">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-500 to-indigo-600 flex items-center justify-center text-white text-xl shadow-lg shadow-cyan-500/20">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <h1 class="font-display font-bold text-2xl text-white">Reseller Gift Card Store</h1>
                <p class="text-xs text-slate-400">Issue Instant Digital Vouchers & E-Gift Cards for Customers</p>
            </div>
        </div>

        <!-- Outlet Selector & Wallet Widget -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Outlet Selector Form -->
            <form action="{{ route('merchant.switch') }}" method="POST" class="flex items-center">
                @csrf
                <select name="merchant_id" onchange="this.form.submit()" class="bg-slate-900/90 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500 font-semibold cursor-pointer">
                    @forelse($allMerchants as $m)
                        <option value="{{ $m->id }}" {{ ($merchant && $merchant->id === $m->id) ? 'selected' : '' }}>
                            🏪 {{ $m->name }} ({{ $m->mobile }})
                        </option>
                    @empty
                        <option value="">No Verified Merchants (Use Admin Panel)</option>
                    @endforelse
                </select>
            </form>

            <!-- Wallet Balance Badge -->
            <div class="glass-card px-4 py-2.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-semibold text-emerald-400/80">Outlet Balance</div>
                    <div class="font-display font-bold text-base text-white">₹{{ number_format($merchant->wallet_balance ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Catalog Search & Filter Tabs -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-2 sm:pb-0 w-full sm:w-auto">
            <button onclick="filterCategory('all')" class="cat-tab px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 text-white shadow-md">All Brands</button>
            <button onclick="filterCategory('Shopping')" class="cat-tab px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800/80 text-slate-300 hover:text-white border border-slate-700">Shopping</button>
            <button onclick="filterCategory('Food & Dining')" class="cat-tab px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800/80 text-slate-300 hover:text-white border border-slate-700">Food & Dining</button>
            <button onclick="filterCategory('Fashion')" class="cat-tab px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800/80 text-slate-300 hover:text-white border border-slate-700">Fashion</button>
            <button onclick="filterCategory('Entertainment')" class="cat-tab px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800/80 text-slate-300 hover:text-white border border-slate-700">Entertainment</button>
            <button onclick="filterCategory('Electronics')" class="cat-tab px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800/80 text-slate-300 hover:text-white border border-slate-700">Electronics</button>
        </div>

        <!-- Search Bar -->
        <div class="relative w-full sm:w-64">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
            <input type="text" id="searchInput" onkeyup="searchCards()" placeholder="Search brand (e.g. Amazon, Flipkart)..." class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-9 pr-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
        </div>
    </div>

    <!-- Gift Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="cardsGrid">
        @foreach($giftCards as $card)
            <div class="gift-card-item glass-card rounded-2xl overflow-hidden border border-slate-800/80 flex flex-col justify-between" data-category="{{ $card['category'] ?? 'Shopping' }}" data-name="{{ strtolower($card['name']) }}">
                <div>
                    <!-- Brand Image & Discount Badge -->
                    <div class="relative h-40 w-full overflow-hidden bg-slate-900">
                        <img src="{{ $card['image'] }}" alt="{{ $card['name'] }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500 text-slate-950 shadow-md">
                            {{ $card['discount_pct'] ?? 3.0 }}% MARGIN
                        </span>
                        <span class="absolute bottom-3 left-3 font-mono text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-900/80 text-slate-300 border border-slate-700">
                            CODE: {{ $card['code'] }}
                        </span>
                    </div>

                    <!-- Details -->
                    <div class="p-4 space-y-2">
                        <h3 class="font-display font-bold text-base text-white leading-tight">{{ $card['name'] }}</h3>
                        <p class="text-xs text-slate-400 line-clamp-2">{{ $card['description'] }}</p>
                        <div class="pt-2 flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-800/60">
                            <span>Range: ₹{{ number_format($card['min_amount']) }} - ₹{{ number_format($card['max_amount']) }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 pt-0">
                    <button onclick="openPurchaseModal('{{ $card['code'] }}', '{{ addslashes($card['name']) }}', {{ $card['min_amount'] }}, {{ $card['max_amount'] }})" class="glow-button w-full py-2.5 rounded-xl text-xs font-bold text-white flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cart-shopping"></i> Buy Gift Card
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Merchant Recent Orders Table -->
    <div class="glass-panel rounded-2xl p-6 border border-slate-800 space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div>
                <h2 class="font-display font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-clock-history text-indigo-400"></i> Outlet Order History
                </h2>
                <p class="text-xs text-slate-400">Issued Gift Vouchers & Instant Receipts</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                        <th class="pb-3 px-3">Date / Order ID</th>
                        <th class="pb-3 px-3">Brand Voucher</th>
                        <th class="pb-3 px-3">Customer</th>
                        <th class="pb-3 px-3">Amount</th>
                        <th class="pb-3 px-3">Voucher Card & PIN</th>
                        <th class="pb-3 px-3 text-right">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-200">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-3">
                                <div>{{ $t->created_at->format('d M Y, h:i A') }}</div>
                                <div class="text-[10px] font-mono text-indigo-300">{{ $t->order_id ?? $t->client_referenceId }}</div>
                            </td>
                            <td class="py-3 px-3 font-semibold text-white">
                                {{ $t->provider_name }}
                            </td>
                            <td class="py-3 px-3">
                                <div>{{ $t->fname }} {{ $t->lname }}</div>
                                <div class="text-[10px] text-slate-400">{{ $t->mobile }}</div>
                            </td>
                            <td class="py-3 px-3 font-bold text-white font-display text-sm">
                                ₹{{ number_format($t->amount, 2) }}
                            </td>
                            <td class="py-3 px-3 font-mono">
                                @if($t->card_no)
                                    <div class="text-cyan-300">CARD: {{ $t->card_no }}</div>
                                    <div class="text-emerald-400 text-[11px]">PIN: {{ $t->pin }}</div>
                                @else
                                    <span class="text-rose-400">FAILED</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                <button onclick="showReceiptModal({{ $t->id }})" class="px-3 py-1.5 rounded-lg bg-indigo-600/20 text-indigo-300 border border-indigo-500/30 hover:bg-indigo-600/30 text-xs font-semibold">
                                    <i class="fa-solid fa-receipt mr-1"></i> View Receipt
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">No vouchers purchased yet from this outlet. Select a card above to issue your first gift card!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal 1: Purchase Gift Card -->
<div id="purchaseModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-panel w-full max-w-lg rounded-2xl p-6 border border-slate-700/80 relative shadow-2xl animate-fade-in custom-scrollbar max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-gift"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-xl text-white" id="modalBrandTitle">Purchase Gift Card</h3>
                    <p class="text-xs text-slate-400" id="modalBrandSub">Instant Voucher Generation</p>
                </div>
            </div>
            <button onclick="closePurchaseModal()" class="text-slate-400 hover:text-white p-1"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>

        <form id="purchaseForm" onsubmit="submitPurchase(event)" class="mt-5 space-y-4">
            @csrf
            <input type="hidden" id="p_code" name="code">

            <!-- Amount Pills -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Select Voucher Amount (₹)</label>
                <div class="grid grid-cols-4 gap-2 mb-2">
                    <button type="button" onclick="setAmount(250)" class="py-2 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-300 hover:border-indigo-500">₹250</button>
                    <button type="button" onclick="setAmount(500)" class="py-2 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-300 hover:border-indigo-500">₹500</button>
                    <button type="button" onclick="setAmount(1000)" class="py-2 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-300 hover:border-indigo-500">₹1,000</button>
                    <button type="button" onclick="setAmount(2500)" class="py-2 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-300 hover:border-indigo-500">₹2,500</button>
                </div>
                <input type="number" id="p_amount" name="amount" value="500" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm font-bold text-white focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Customer Details -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Customer First Name *</label>
                    <input type="text" name="fname" value="Rakesh" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Customer Last Name *</label>
                    <input type="text" name="lname" value="Mittal" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Customer Mobile *</label>
                    <input type="tel" name="mobile" value="9999988888" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Customer Email *</label>
                    <input type="email" name="email" value="rakesh.mittal@outlook.in" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Gift Card Message</label>
                <input type="text" name="giftMessage" value="Congratulations on your Gift Voucher!" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <button type="button" onclick="closePurchaseModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white">Cancel</button>
                <button type="submit" class="glow-button px-6 py-2.5 rounded-xl text-xs font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-bolt"></i> Confirm & Generate Voucher
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Printable Voucher Receipt -->
<div id="receiptModal" class="fixed inset-0 z-50 hidden bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-panel w-full max-w-md rounded-3xl p-6 border border-cyan-500/30 relative shadow-2xl animate-fade-in text-slate-100 space-y-5">
        <button onclick="closeReceiptModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white p-1"><i class="fa-solid fa-xmark text-xl"></i></button>

        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 to-cyan-500 text-slate-950 flex items-center justify-center mx-auto text-2xl shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h2 class="font-display font-extrabold text-2xl text-white">Voucher Issued!</h2>
            <p class="text-xs text-emerald-400 font-semibold" id="r_provider">Flipkart E-Gift Card</p>
        </div>

        <!-- Voucher Card Design -->
        <div class="p-5 rounded-2xl bg-gradient-to-br from-indigo-950 via-slate-900 to-slate-950 border border-indigo-500/30 space-y-4 shadow-xl">
            <div class="flex items-center justify-between text-xs text-slate-400 border-b border-slate-800/80 pb-3">
                <span>VOUCHER VALUE</span>
                <span class="font-display font-bold text-xl text-emerald-400" id="r_amount">₹2,500.00</span>
            </div>

            <!-- Card Number -->
            <div class="space-y-1">
                <div class="text-[10px] uppercase font-semibold text-slate-400">Card Number</div>
                <div class="flex items-center justify-between bg-slate-950/80 p-2.5 rounded-xl border border-slate-800">
                    <span class="font-mono text-sm font-bold text-cyan-300 tracking-wider" id="r_cardno">600373667274627461</span>
                    <button onclick="copyToClipboard('r_cardno')" class="text-xs text-slate-400 hover:text-white px-2 py-1 bg-slate-800 rounded-lg"><i class="fa-solid fa-copy"></i></button>
                </div>
            </div>

            <!-- PIN & Expiry -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <div class="text-[10px] uppercase font-semibold text-slate-400">Voucher PIN</div>
                    <div class="flex items-center justify-between bg-slate-950/80 p-2 rounded-xl border border-slate-800">
                        <span class="font-mono text-sm font-bold text-emerald-400 tracking-widest" id="r_pin">388462</span>
                        <button onclick="copyToClipboard('r_pin')" class="text-xs text-slate-400 hover:text-white px-1.5 py-0.5 bg-slate-800 rounded"><i class="fa-solid fa-copy"></i></button>
                    </div>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-semibold text-slate-400">Expiry Date</div>
                    <div class="bg-slate-950/80 p-2 rounded-xl border border-slate-800 text-center font-mono text-xs font-semibold text-amber-300" id="r_exp">
                        2027-01-18
                    </div>
                </div>
            </div>

            <!-- Customer & Order details -->
            <div class="text-[11px] text-slate-400 border-t border-slate-800/80 pt-3 space-y-1">
                <div class="flex justify-between"><span>Issued To:</span> <span class="text-white font-semibold" id="r_customer">Rakesh Mittal</span></div>
                <div class="flex justify-between"><span>Order ID:</span> <span class="text-indigo-300 font-mono" id="r_orderid">ANBLU1477264</span></div>
            </div>
        </div>

        <!-- Receipt Actions -->
        <div class="flex items-center gap-2 pt-2">
            <button onclick="window.print()" class="flex-1 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold flex items-center justify-center gap-2">
                <i class="fa-solid fa-print"></i> Print
            </button>
            <button onclick="shareWhatsApp()" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center justify-center gap-2">
                <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentVoucherData = null;

    function filterCategory(cat) {
        const tabs = document.querySelectorAll('.cat-tab');
        tabs.forEach(t => t.classList.remove('bg-indigo-600', 'text-white'));

        event.target.classList.add('bg-indigo-600', 'text-white');

        const items = document.querySelectorAll('.gift-card-item');
        items.forEach(item => {
            if (cat === 'all' || item.getAttribute('data-category') === cat) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function searchCards() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const items = document.querySelectorAll('.gift-card-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name');
            if (name.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function openPurchaseModal(code, name, min, max) {
        document.getElementById('p_code').value = code;
        document.getElementById('modalBrandTitle').innerText = name;
        document.getElementById('modalBrandSub').innerText = `Code: ${code} (Min: ₹${min} - Max: ₹${max})`;
        document.getElementById('purchaseModal').classList.remove('hidden');
    }

    function closePurchaseModal() {
        document.getElementById('purchaseModal').classList.add('hidden');
    }

    function setAmount(val) {
        document.getElementById('p_amount').value = val;
    }

    function submitPurchase(e) {
        e.preventDefault();
        const form = document.getElementById('purchaseForm');
        const formData = new FormData(form);

        fetch("{{ route('merchant.purchase') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closePurchaseModal();
                currentVoucherData = data.transaction;
                populateReceipt(data.transaction);
                
                // Trigger Confetti Celebration
                confetti({
                    particleCount: 120,
                    spread: 70,
                    origin: { y: 0.6 }
                });

                document.getElementById('receiptModal').classList.remove('hidden');
            } else {
                alert("Purchase Error: " + data.message);
            }
        })
        .catch(err => alert("Gift Card Order Request Failed: " + err));
    }

    function showReceiptModal(id) {
        fetch(`/merchant/receipt/${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                currentVoucherData = data.transaction;
                populateReceipt(data.transaction);
                document.getElementById('receiptModal').classList.remove('hidden');
            }
        });
    }

    function populateReceipt(tx) {
        document.getElementById('r_provider').innerText = tx.provider_name;
        document.getElementById('r_amount').innerText = '₹' + parseFloat(tx.amount).toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('r_cardno').innerText = tx.card_no || 'N/A';
        document.getElementById('r_pin').innerText = tx.pin || 'N/A';
        document.getElementById('r_exp').innerText = tx.card_exp || 'N/A';
        document.getElementById('r_customer').innerText = `${tx.fname} ${tx.lname} (${tx.mobile})`;
        document.getElementById('r_orderid').innerText = tx.order_id || tx.client_referenceId;
    }

    function closeReceiptModal() {
        document.getElementById('receiptModal').classList.add('hidden');
        location.reload();
    }

    function copyToClipboard(id) {
        const text = document.getElementById(id).innerText;
        navigator.clipboard.writeText(text);
        alert("Copied to clipboard: " + text);
    }

    function shareWhatsApp() {
        if (!currentVoucherData) return;
        const text = `🎁 *${currentVoucherData.provider_name} Gift Voucher*\n` +
                     `Amount: ₹${currentVoucherData.amount}\n` +
                     `Card No: ${currentVoucherData.card_no}\n` +
                     `PIN: ${currentVoucherData.pin}\n` +
                     `Expiry: ${currentVoucherData.card_exp}\n\n` +
                     `Enjoy your gift voucher!`;
        window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
    }
</script>
@endpush
