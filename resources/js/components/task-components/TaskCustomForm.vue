<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Button from '../ui/button/Button.vue';
import { ButtonGroup } from '../ui/button-group';
import { Plus } from 'lucide-vue-next';



const Inputs = [
    {
        name: 'Text',
        type: 'text',
    },
    {
        name: 'Input number',
        type: 'number',

    },
    {
        name: 'TextArea',
        type: 'textarea',
    }
];

</script>
<template>

    <div class="flex">
        <div class="flex gap-4 ml-auto">
            <div v-for="input in Inputs" :key="input.name">
            <Button disabled>
                <Plus /> {{ input.name }}
            </Button>
            </div>
        </div>
    </div>

</template>