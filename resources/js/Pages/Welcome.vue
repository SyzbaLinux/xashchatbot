<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import GuestLayout from "@/Layouts/GuestLayout.vue";

const page = usePage();

const getDashboardRoute = (role: string) => {
  return role === 'admin' ? route('admin.dashboard') : route('user.dashboard');
};
</script>

<template>
    <GuestLayout>
        <Head title="Welcome">
            <link rel="preconnect" href="https://rsms.me/" />
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        </Head>

        <div
            class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-slate-50 via-emerald-50 to-green-50 p-6 text-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 dark:text-slate-50"
        >
            <!-- Animated background elements -->
            <div class="absolute inset-0 overflow-hidden opacity-30">
                <div class="absolute -left-4 top-0 h-72 w-72 animate-pulse rounded-full bg-emerald-400 mix-blend-multiply blur-3xl filter dark:bg-emerald-600"></div>
                <div class="animation-delay-2000 absolute -right-4 top-0 h-72 w-72 animate-pulse rounded-full bg-green-400 mix-blend-multiply blur-3xl filter dark:bg-green-600"></div>
                <div class="animation-delay-4000 absolute -bottom-8 left-20 h-72 w-72 animate-pulse rounded-full bg-emerald-500 mix-blend-multiply blur-3xl filter dark:bg-emerald-700"></div>
            </div>

            <!-- Main content -->
            <div class="relative z-10 w-full max-w-4xl">
                <main class="flex flex-col items-center justify-center space-y-8 text-center">
                    <!-- Logo with subtle animation -->
                    <div class="animate-fade-in-up">
                        <img
                            src="/zblogo.png"
                            alt="Cash Position & Requisition Management"
                            class="mb-6 h-16 drop-shadow-lg transition-transform duration-300 hover:scale-105 lg:h-20"
                        />
                    </div>

                    <!-- Heading with gradient text -->
                    <div class="animate-fade-in-up animation-delay-200 space-y-4">
                        <h1 class="bg-gradient-to-r  from-slate-900 via-emerald-700 to-green-900 bg-clip-text text-4xl font-bold text-transparent dark:from-slate-100 dark:via-emerald-300 dark:to-green-300 lg:text-6xl">
                            Queue Management Simplified
                        </h1>
                        <p class="mx-auto max-w-2xl text-lg text-slate-600 dark:text-slate-400 lg:text-xl">
                            Real-time branch queue handling with ticket printing and audio announcements for a seamless banking experience.
                        </p>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="animate-fade-in-up animation-delay-600 mt-12 flex flex-col items-center gap-4 sm:flex-row">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="getDashboardRoute($page.props.auth.user.role)"
                            class="group relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 px-8 py-4 font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl dark:from-emerald-500 dark:to-green-500"
                        >
                            <span class="relative z-10">Go to Dashboard</span>
                            <svg class="relative z-10 h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-emerald-700 to-green-700 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="group relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 px-8 py-4 font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl dark:from-emerald-500 dark:to-green-500"
                            >
                                <span class="relative z-10">Get Started</span>
                                <svg class="relative z-10 h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                <div class="absolute inset-0 -z-10 bg-gradient-to-r from-emerald-700 to-green-700 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                            </Link>
                        </template>
                    </div>
                </main>
            </div>
        </div>
    </GuestLayout>

</template>

<style scoped>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
    opacity: 0;
}

.animation-delay-200 {
    animation-delay: 0.2s;
}

.animation-delay-400 {
    animation-delay: 0.4s;
}

.animation-delay-600 {
    animation-delay: 0.6s;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
