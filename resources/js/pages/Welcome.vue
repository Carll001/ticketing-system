<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import task from '@/routes/task';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Users, ListChecks, Shield, FileText, Handshake } from 'lucide-vue-next';
import { computed } from 'vue';

// withDefaults(
//     defineProps<{
//         canRegister: boolean;
//     }>(),
//     {
//         canRegister: true,
//     },
// );

const page = usePage();
const auth = computed(() => page.props.auth);

const canViewDashboard = computed(() => {
    return auth.value?.permissions?.includes('can view dashboard') ?? false;
});
</script>

<template>
    <Head title="Welcome" />
    
    <div class="min-h-screen bg-[#0F0F0F]">
        <div class="grid lg:grid-cols-2 min-h-screen">
            <!-- Left Panel -->
            <div class="flex items-center justify-center p-6 lg:p-12">
                <div class="w-full max-w-lg space-y-6">
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-white mb-3">
                            Welcome to Ticketing System
                        </h1>
                        <p class="text-base text-gray-400">
                            Empowering teams through comprehensive task management.
                        </p>
                    </div>

                    <!-- Features -->
                    <div class="space-y-4">
                        <!-- Team Collaboration -->
                        <div class="flex gap-3">
                            <div class="bg-blue-500/10 rounded-lg p-2.5 h-fit">
                                <Users class="h-5 w-5 text-blue-500" />
                            </div>
                            <div>
                                <h3 class="text-white font-semibold text-base mb-1">Team Collaboration</h3>
                                <p class="text-gray-400 text-sm leading-relaxed">
                                    Maintain comprehensive records of tasks and team members, including assignments and progress tracking.
                                </p>
                            </div>
                        </div>

                        <!-- Task Management -->
                        <div class="flex gap-3">
                            <div class="bg-green-500/10 rounded-lg p-2.5 h-fit">
                                <ListChecks class="h-5 w-5 text-green-500" />
                            </div>
                            <div>
                                <h3 class="text-white font-semibold text-base mb-1">Task Management</h3>
                                <p class="text-gray-400 text-sm leading-relaxed">
                                    Streamline task creation and tracking with automated workflows and cost monitoring.
                                </p>
                            </div>
                        </div>

                        <!-- Secure & Compliant -->
                        <div class="flex gap-3">
                            <div class="bg-cyan-500/10 rounded-lg p-2.5 h-fit">
                                <Shield class="h-5 w-5 text-cyan-500" />
                            </div>
                            <div>
                                <h3 class="text-white font-semibold text-base mb-1">Secure & Compliant</h3>
                                <p class="text-gray-400 text-sm leading-relaxed">
                                    Role-based access control ensures data security while maintaining compliance.
                                </p>
                            </div>
                        </div>

                        <!-- Administrative Tools -->
                        <div class="flex gap-3">
                            <div class="bg-amber-500/10 rounded-lg p-2.5 h-fit">
                                <FileText class="h-5 w-5 text-amber-500" />
                            </div>
                            <div>
                                <h3 class="text-white font-semibold text-base mb-1">Administrative Tools</h3>
                                <p class="text-gray-400 text-sm leading-relaxed">
                                    Manage departments, permissions, and generate comprehensive reports.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="pt-2">
                        <Link
                            v-if="$page.props.auth.user && canViewDashboard"
                            :href="dashboard()"
                            class="inline-block rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors"
                        >
                            Go to Dashboard
                        </Link>
                        <Link
                            v-else-if="$page.props.auth.user && !canViewDashboard"
                            :href="task.index()"
                            class="inline-block rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors"
                        >
                            Go to Task
                        </Link>
                        <Link
                            v-else-if="!$page.props.auth.user"
                            :href="login()"
                            class="inline-block rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors"
                        >
                            Get Started
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Right Panel -->
            <div class="hidden lg:flex items-center justify-center p-6">
                <div class="w-full h-[85vh] rounded-2xl bg-gradient-to-br from-[#4F46E5] via-[#5B52E8] to-[#6B63EB] p-10 flex flex-col items-center justify-center relative overflow-hidden">
                    <!-- Decorative Background Circle -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-56 h-56 rounded-full bg-white/5 backdrop-blur-sm"></div>
                    </div>

                    <!-- Icon -->
                    <div class="relative z-10 mb-6 w-20 h-20 rounded-full bg-white/20 flex items-center justify-center">
                        <Handshake class="h-10 w-10 text-white" />
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 text-center">
                        <h2 class="text-2xl font-bold text-white mb-2">
                            Task Management System
                        </h2>
                        <p class="text-lg text-blue-100 mb-1.5">
                            Your Organization
                        </p>
                        <p class="text-sm text-blue-200">
                            Connecting Teams, Transforming Workflows
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>