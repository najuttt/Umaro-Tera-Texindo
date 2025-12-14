<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#0B2447] px-4">

        <div class="bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl 
                    rounded-2xl p-8 md:p-10 w-full max-w-md text-white">

            <!-- LOGO UMARO -->
            <div class="flex flex-col items-center mb-6">

                <h2 class="text-3xl font-bold tracking-wide">
                    UMARO <span class="text-[#D4A017]">Login</span>
                </h2>

                <p class="text-sm text-gray-200 mt-1">
                    Masuk untuk melanjutkan ke sistem.
                </p>
            </div>

            <!-- ALERT -->
            <div id="alertBox"
                 class="hidden mb-4 px-4 py-3 rounded-lg text-sm font-medium text-white opacity-0 transition-all">
            </div>

            <!-- FORM -->
            <form id="ajaxLoginForm" class="space-y-5">
                @csrf

                <div>
                    <label class="text-sm font-medium text-gray-200">Email</label>
                    <input type="email" name="email" required
                        class="mt-1 w-full px-4 py-3 bg-white/20 border border-white/30
                               rounded-lg text-white placeholder-gray-300 focus:ring-2
                               focus:ring-[#D4A017] outline-none transition">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-200">Password</label>
                    <input type="password" name="password" required minlength="8"
                        class="mt-1 w-full px-4 py-3 bg-white/20 border border-white/30
                               rounded-lg text-white placeholder-gray-300 focus:ring-2
                               focus:ring-[#D4A017] outline-none transition">
                </div>

                <button id="loginSubmitBtn" type="submit"
                    class="w-full py-3 rounded-lg font-semibold bg-[#D4A017] text-white 
                           hover:bg-[#b58a13] transition-all shadow-md hover:shadow-lg">
                    MASUK
                </button>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const form = document.getElementById('ajaxLoginForm');
            const alertBox = document.getElementById('alertBox');
            const submitBtn = document.getElementById('loginSubmitBtn');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                hideAlert();

                submitBtn.disabled = true;
                submitBtn.textContent = "Memproses...";

                try {
                    const res = await fetch("{{ route('login') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": form._token.value,
                        },
                        body: JSON.stringify({
                            email: form.email.value,
                            password: form.password.value,
                        }),
                    });

                    const data = await res.json();

                    if (res.ok && data.success) {
                        showAlert("success", data.message);
                        setTimeout(() => window.location.href = data.redirect, 800);
                    } else {
                        showAlert("danger", data.message ?? "Email atau password salah.");
                    }

                } catch (e) {
                    showAlert("info", "Terjadi kesalahan jaringan.");
                }

                submitBtn.disabled = false;
                submitBtn.textContent = "MASUK";
            });

            function showAlert(type, msg) {
                alertBox.textContent = msg;
                alertBox.className =
                    "mb-4 px-4 py-3 rounded-lg text-sm font-medium transition-all opacity-100 " +
                    {
                        success: "bg-green-500",
                        danger: "bg-red-500",
                        info: "bg-blue-500"
                    }[type];

                alertBox.classList.remove("hidden");
            }

            function hideAlert() {
                alertBox.classList.add("opacity-0");
                setTimeout(() => alertBox.classList.add("hidden"), 300);
            }
        });
    </script>
</x-guest-layout>