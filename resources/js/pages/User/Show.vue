<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { User, Task } from '@/types';
import ShowUser from '@/components/user-component/ShowUser.vue';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    user: User;
    tasks?: Task[];
}>();
</script>

<template>
    <Head :title="`${user.name} - Profile`" />
    <AppLayout>
        <div class="flex flex-col flex-1 gap-4 p-4">
            <!-- Header -->
            <section class="flex items-center gap-4">
                <Button variant="ghost" size="sm" @click="router.visit('/user')">
                    <ArrowLeft class="h-4 w-4" />
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">{{ user.name }}</h1>
                    <p class="text-sm text-muted-foreground">User Profile</p>
                </div>
            </section>

            <!-- User Profile and Tasks -->
            <section>
                <ShowUser :user="props.user" :tasks="props.tasks" />
            </section>
        </div>
    </AppLayout>
</template>
