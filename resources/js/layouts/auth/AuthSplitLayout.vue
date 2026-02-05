<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const name = page.props.name;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative min-h-screen grid lg:grid-cols-2">
        <div class="relative hidden lg:flex flex-col bg-zinc-900 p-12 text-white overflow-hidden">
            <div class="absolute inset-0 z-0 opacity-40">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 right-0 h-96 w-96 bg-purple-600/20 blur-[100px] rounded-full"></div>
            </div>

            <div class="relative z-10">
                <Link :href="home()" class="flex items-center gap-3 text-xl font-semibold tracking-tight">
                    <div>
                        <AppLogoIcon class="size-9 fill-white" />
                    </div>
                    <span>{{ name }}</span>
                </Link>
            </div>

            <div class="relative z-10 mt-auto">
                <blockquote class="space-y-4">
                    <p class="text-lg font-light leading-relaxed italic">
                        "This platform has completely transformed how we manage our workflow. Simple, elegant, and
                        powerful."
                    </p>
                </blockquote>
            </div>
        </div>

        <div class="flex items-center justify-center p-8 bg-white dark:bg-zinc-950">
            <div class="mx-auto w-full max-w-[400px] space-y-8">
                <div class="flex flex-col space-y-3 text-center lg:text-left">
                    <div class="lg:hidden flex justify-center mb-4">
                        <AppLogoIcon class="size-10 fill-indigo-600" />
                    </div>

                    <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white" v-if="title">
                        {{ title }}
                    </h1>
                    <p class="text-base text-zinc-500 dark:text-zinc-400" v-if="description">
                        {{ description }}
                    </p>
                </div>

                <div class="grid gap-6">
                    <slot />
                </div>

                <p class="px-8 text-center text-sm text-zinc-500">
                    By clicking continue, you agree to our
                    <a href="#" class="underline underline-offset-4 hover:text-primary">Terms of Service</a>.
                </p>
            </div>
        </div>
    </div>
</template>