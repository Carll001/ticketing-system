<script setup lang="ts">
import taskLink from '@/routes/task';
import { Button } from '../ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '../ui/dialog';
import { router } from '@inertiajs/vue3';
import stepLink from '@/routes/step';
import { Step } from '@/types';
import { Trash } from 'lucide-vue-next';

const props = defineProps<{
    step: Step
}>();

const deleteStep = () => {
    router.delete(stepLink.delete({ task: props.step.task_id, step: props.step.id }).url, {
        preserveScroll: true,
    })
}

</script>
<template>
    <Dialog>
    <DialogTrigger as-child>
        <Button size="sm" variant="destructive">Delete</Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Are you absolutely sure?</DialogTitle>
        <DialogDescription>
          This action cannot be undone. This will permanently delete your account
          and remove your data from our servers.
        </DialogDescription>
      </DialogHeader>
      <DialogFooter>
        <DialogClose as-child>
            <Button variant="ghost">Close</Button>
        </DialogClose>
        <Button @click="deleteStep">Delete</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>