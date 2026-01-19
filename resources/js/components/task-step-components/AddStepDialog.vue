<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import stepLink from '@/routes/step';
import taskLink from '@/routes/task';
import { Task } from '@/types';
import { Form, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const openAddStep = ref(false);

const props = defineProps<{
    task: {data: Task}
}>();

const form = useForm({
    task_id: props.task.data.id,
    title: '',
    description: '',
});

const storeTaskStep = () => {
    form.post(stepLink.store(props.task.data.id).url, {
        onSuccess: () => {
            openAddStep.value = false;
        }
    })
}

</script>
<template>
    <Dialog v-model:open="openAddStep">
        <DialogTrigger as-child>
            <Button size="sm">Add step</Button>
        </DialogTrigger>
        <DialogContent>
            <Form @submit.prevent="storeTaskStep" class="space-y-4">
            <DialogHeader>
                <DialogTitle>Add step</DialogTitle>
                <DialogDescription>add step to task</DialogDescription>
            </DialogHeader>
            <div class="space-y-4">
                <Label for="step-title">Step title</Label>
                <Input id="step-title" v-model="form.title" placeholder="task title"/>
                <InputError :message="form.errors.title"/>
            </div>
            <div class="space-y-4">
                <Label for="step-description">Step description</Label>
                <Input id="step-description" v-model="form.description" placeholder="task description"/>
                <InputError :message="form.errors.description"/>
            </div>
            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="ghost" >Discard</Button>
                </DialogClose>
                <Button>Add step</Button>
            </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>