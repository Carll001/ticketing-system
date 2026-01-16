<script setup lang="ts">
    import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import task from '@/routes/task';
import { Form, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    description: '',
    assigned_to: '',
});

const createTask = () => {
    form.post(task.store().url, {
        onSuccess: () => {

        }
    })
}

</script>
<template>
    <Dialog>
        
            <DialogTrigger as-child>
                <Button variant="outline">
                    Create Task
                </Button>
            </DialogTrigger>
            <DialogContent>
                <Form
            @submit.prevent="createTask"
            >
                <DialogHeader>
                <DialogTitle>Create new task</DialogTitle>
                <DialogDescription>
                    create a new task.
                </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4">
                    <Label for="task-title">Task Title</Label>
                    <Input id="task-title"/>
                </div>
                <DialogFooter>
                <DialogClose as-child>
                    <Button variant="outline">
                    Cancel
                    </Button>
                </DialogClose>
                <Button type="submit">
                    Save changes
                </Button>
                </DialogFooter>
                </Form>
            </DialogContent>
        
  </Dialog>
</template>