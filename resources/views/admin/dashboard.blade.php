@extends('layouts.app')

@section('title', 'Super Admin Dashboard | AeronPay SaaS Reseller')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-panel p-6 rounded-2xl border border-indigo-500/20">
        <div>
            <h1 class="font-display font-bold text-2xl md:text-3xl text-white flex items-center gap-3">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 via-cyan-400 to-white">Super Admin Management</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Manage e-KYC Merchant Onboarding, Reseller Outlets, Wallet Credit & AeronPay API Services.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openOnboardModal()" class="glow-button px-5 py-2.5 rounded-xl text-xs font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-sm"></i> Onboard Merchant (e-KYC)
            </button>
        </div>
    </div>

    <!-- Overview Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Metric 1 -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Merchants</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 flex items-center justify-center">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <h3 class="text-2xl font-bold font-display text-white">{{ number_format($totalMerchants) }}</h3>
                <span class="text-xs text-emerald-400 font-semibold bg-emerald-500/10 px-2 py-0.5 rounded-md">{{ $verifiedMerchants }} Verified</span>
            </div>
        </div>

        <!-- Metric 2 -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">System Wallet Volume</span>
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center">
                    <i class="fa-solid fa-wallet text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold font-display text-white">₹{{ number_format($totalWalletBalance, 2) }}</h3>
            </div>
        </div>

        <!-- Metric 3 -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Vouchers Issued</span>
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/30 text-purple-400 flex items-center justify-center">
                    <i class="fa-solid fa-gift text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold font-display text-white">{{ number_format($totalTransactions) }}</h3>
            </div>
        </div>

        <!-- Metric 4 -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Voucher Gross Volume</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold font-display text-white">₹{{ number_format($totalVoucherVolume, 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Merchant Directory Table -->
    <div class="glass-panel rounded-2xl p-6 border border-slate-800 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-800">
            <div>
                <h2 class="font-display font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-store text-indigo-400"></i> Merchant Outlets Directory
                </h2>
                <p class="text-xs text-slate-400">Digital e-KYC Verified Reseller Accounts</p>
            </div>
            <button onclick="openOnboardModal()" class="px-4 py-2 rounded-xl bg-indigo-600/20 text-indigo-300 border border-indigo-500/30 hover:bg-indigo-600/30 text-xs font-semibold">
                + Add Merchant
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                        <th class="pb-3 px-3">Merchant / Outlet</th>
                        <th class="pb-3 px-3">Mobile & Email</th>
                        <th class="pb-3 px-3">PAN & Aadhaar</th>
                        <th class="pb-3 px-3">Bank Details</th>
                        <th class="pb-3 px-3">e-KYC Status</th>
                        <th class="pb-3 px-3">Wallet Balance</th>
                        <th class="pb-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-200">
                    @forelse($merchants as $m)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-3">
                                <div class="font-semibold text-white">{{ $m->name }}</div>
                                <div class="text-[10px] font-mono text-slate-400">{{ $m->client_referenceId }}</div>
                            </td>
                            <td class="py-3.5 px-3">
                                <div><i class="fa-solid fa-phone text-slate-500 mr-1"></i>{{ $m->mobile }}</div>
                                <div class="text-[11px] text-slate-400">{{ $m->email }}</div>
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="font-mono text-indigo-300">PAN: {{ $m->pan }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">AADHAAR: {{ substr($m->aadhaar_number, 0, 4) }}-XXXX-{{ substr($m->aadhaar_number, -4) }}</div>
                            </td>
                            <td class="py-3.5 px-3">
                                <div>A/c: {{ $m->bank_account }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">IFSC: {{ $m->ifsc }}</div>
                            </td>
                            <td class="py-3.5 px-3">
                                @if($m->status === 'VERIFIED')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> VERIFIED
                                    </span>
                                @elseif($m->status === 'PENDING_OTP')
                                    <button onclick="openOtpModal('{{ $m->client_referenceId }}', '{{ $m->refid }}', '{{ $m->hash }}')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30 hover:bg-amber-500/20">
                                        <i class="fa-solid fa-clock text-[10px]"></i> ENTER OTP
                                    </button>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30">REJECTED</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 font-semibold font-display text-white text-sm">
                                ₹{{ number_format($m->wallet_balance, 2) }}
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <button onclick="openAddWalletModal({{ $m->id }}, '{{ addslashes($m->name) }}')" class="px-3 py-1.5 rounded-lg bg-emerald-600/20 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-600/30 text-xs font-semibold flex items-center gap-1.5 ml-auto">
                                    <i class="fa-solid fa-plus-circle"></i> Add Credit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                <i class="fa-solid fa-user-slash text-2xl mb-2"></i>
                                <p>No merchants onboarded yet. Click "Onboard Merchant (e-KYC)" to register your first outlet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Master Transaction Logs -->
    <div class="glass-panel rounded-2xl p-6 border border-slate-800 space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div>
                <h2 class="font-display font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-cyan-400"></i> Master Reseller Transaction Ledger
                </h2>
                <p class="text-xs text-slate-400">Recent Gift Card Orders Across Outlets</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                        <th class="pb-3 px-3">Date / Ref ID</th>
                        <th class="pb-3 px-3">Merchant</th>
                        <th class="pb-3 px-3">Gift Card Brand</th>
                        <th class="pb-3 px-3">Customer</th>
                        <th class="pb-3 px-3">Amount</th>
                        <th class="pb-3 px-3">Voucher Details</th>
                        <th class="pb-3 px-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-200">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-3">
                                <div>{{ $t->created_at->format('d M Y, h:i A') }}</div>
                                <div class="text-[10px] font-mono text-slate-400">{{ $t->client_referenceId }}</div>
                            </td>
                            <td class="py-3 px-3 font-semibold text-slate-300">
                                {{ $t->merchant->name ?? 'Outlet #' . $t->merchant_id }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-semibold">{{ $t->provider_name }}</span>
                            </td>
                            <td class="py-3 px-3">
                                <div>{{ $t->fname }} {{ $t->lname }}</div>
                                <div class="text-[10px] text-slate-400">{{ $t->mobile }}</div>
                            </td>
                            <td class="py-3 px-3 font-bold text-white font-display">
                                ₹{{ number_format($t->amount, 2) }}
                            </td>
                            <td class="py-3 px-3">
                                @if($t->card_no)
                                    <div class="font-mono text-cyan-300">CARD: {{ $t->card_no }}</div>
                                    <div class="font-mono text-emerald-400 text-[11px]">PIN: {{ $t->pin }} | EXP: {{ $t->card_exp }}</div>
                                @else
                                    <span class="text-slate-500">N/A</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $t->status === 'SUCCESS' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30' }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-500">No voucher transactions recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal 1: Onboard Merchant e-KYC -->
<div id="onboardModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-panel w-full max-w-2xl rounded-2xl p-6 border border-slate-700/80 relative shadow-2xl animate-fade-in custom-scrollbar max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center">
                    <i class="fa-solid fa-address-card text-lg"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-xl text-white">Merchant e-KYC Signup</h3>
                    <p class="text-xs text-slate-400">AeronPay Real-Time Digital Verification</p>
                </div>
            </div>
            <button onclick="closeOnboardModal()" class="text-slate-400 hover:text-white p-1"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>

        <form id="onboardForm" onsubmit="submitOnboard(event)" class="mt-5 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Outlet / Merchant Business Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Metro Digital Store" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Mobile Number *</label>
                    <input type="tel" name="mobile" required placeholder="10-digit Mobile" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address *</label>
                    <input type="email" name="email" required placeholder="merchant@domain.com" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Aadhaar Card Number *</label>
                    <input type="text" name="aadhaar_number" required placeholder="12-digit Aadhaar" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">PAN Card Number *</label>
                    <input type="text" name="pan" required placeholder="e.g. ABCDE1234F" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white uppercase focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Bank Account Number *</label>
                    <input type="text" name="bank_account" required placeholder="Bank Account No." class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Bank IFSC Code *</label>
                    <input type="text" name="ifsc" required placeholder="e.g. SBIN0001234" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white uppercase focus:outline-none focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Latitude</label>
                        <input type="text" name="latitude" value="28.6139" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Longitude</label>
                        <input type="text" name="longitude" value="77.2090" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-xs text-indigo-300 flex items-start gap-2">
                <i class="fa-solid fa-shield-halved text-indigo-400 mt-0.5"></i>
                <span>All details are directly validated with respective government & banking authorities via AeronPay e-KYC.</span>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <button type="button" onclick="closeOnboardModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white">Cancel</button>
                <button type="submit" class="glow-button px-6 py-2.5 rounded-xl text-xs font-bold text-white flex items-center gap-2">
                    Submit e-KYC & Send OTP <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: OTP Validation -->
<div id="otpModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 border border-slate-700/80 relative shadow-2xl animate-fade-in text-center space-y-4">
        <div class="w-14 h-14 rounded-2xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center mx-auto text-2xl">
            <i class="fa-solid fa-key"></i>
        </div>
        <div>
            <h3 class="font-display font-bold text-xl text-white">Enter Mobile OTP</h3>
            <p class="text-xs text-slate-400 mt-1">Verification code sent to merchant mobile number.</p>
        </div>

        <form id="otpForm" onsubmit="submitOtp(event)" class="space-y-4">
            @csrf
            <input type="hidden" id="otp_client_referenceId" name="client_referenceId">
            <input type="hidden" id="otp_refid" name="refid">
            <input type="hidden" id="otp_hash" name="hash">

            <div class="py-2">
                <input type="text" name="otp" required maxlength="6" placeholder="Enter 6-digit OTP" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-center text-xl font-bold font-mono tracking-widest text-white focus:outline-none focus:border-indigo-500" value="123456">
                <p class="text-[11px] text-amber-400 mt-1">For testing in mock mode, enter any 6-digit code (e.g. 123456).</p>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-3">
                <button type="button" onclick="closeOtpModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white">Cancel</button>
                <button type="submit" class="glow-button w-full py-2.5 rounded-xl text-xs font-bold text-white">
                    Verify e-KYC & Activate Merchant
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Add Wallet Balance -->
<div id="addWalletModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 border border-slate-700/80 relative shadow-2xl animate-fade-in space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="font-display font-bold text-lg text-white flex items-center gap-2">
                <i class="fa-solid fa-coins text-emerald-400"></i> Top-up Reseller Wallet
            </h3>
            <button onclick="closeAddWalletModal()" class="text-slate-400 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="walletForm" onsubmit="submitWallet(event)" class="space-y-4">
            @csrf
            <input type="hidden" id="wallet_merchant_id" name="merchant_id">

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Selected Merchant Outlet</label>
                <input type="text" id="wallet_merchant_name" readonly class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-indigo-300 font-semibold">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Credit Amount (₹) *</label>
                <input type="number" name="amount" min="100" step="100" value="5000" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm font-bold text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-800">
                <button type="button" onclick="closeAddWalletModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white">Cancel</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/30">
                    Add Credit Balance
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openOnboardModal() {
        document.getElementById('onboardModal').classList.remove('hidden');
    }
    function closeOnboardModal() {
        document.getElementById('onboardModal').classList.add('hidden');
    }

    function openOtpModal(clientRefId, refid, hash) {
        document.getElementById('otp_client_referenceId').value = clientRefId;
        document.getElementById('otp_refid').value = refid;
        document.getElementById('otp_hash').value = hash;
        document.getElementById('otpModal').classList.remove('hidden');
    }
    function closeOtpModal() {
        document.getElementById('otpModal').classList.add('hidden');
    }

    function openAddWalletModal(id, name) {
        document.getElementById('wallet_merchant_id').value = id;
        document.getElementById('wallet_merchant_name').value = name;
        document.getElementById('addWalletModal').classList.remove('hidden');
    }
    function closeAddWalletModal() {
        document.getElementById('addWalletModal').classList.add('hidden');
    }

    function submitOnboard(e) {
        e.preventDefault();
        const form = document.getElementById('onboardForm');
        const formData = new FormData(form);

        fetch("{{ route('admin.onboard') }}", {
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
                closeOnboardModal();
                openOtpModal(data.client_referenceId, data.refid, data.hash);
            } else {
                alert("Signup Failed: " + data.message);
            }
        })
        .catch(err => alert("e-KYC Request Failed: " + err));
    }

    function submitOtp(e) {
        e.preventDefault();
        const form = document.getElementById('otpForm');
        const formData = new FormData(form);

        fetch("{{ route('admin.validate_otp') }}", {
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
                alert(data.message);
                location.reload();
            } else {
                alert("OTP Validation Failed: " + data.message);
            }
        })
        .catch(err => alert("OTP Request Failed: " + err));
    }

    function submitWallet(e) {
        e.preventDefault();
        const form = document.getElementById('walletForm');
        const formData = new FormData(form);

        fetch("{{ route('admin.wallet.add') }}", {
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
                alert(data.message);
                location.reload();
            } else {
                alert("Failed to add wallet balance: " + data.message);
            }
        })
        .catch(err => alert("Wallet Request Failed: " + err));
    }
</script>
@endpush
