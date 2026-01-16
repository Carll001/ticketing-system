<script setup lang="ts">
import taskLink from '@/routes/task';
import { Button } from '../ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '../ui/dialog';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    id: string
}>();

const deleteTask = () => {
    router.delete(taskLink.delete(props.id).url)  
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
        <Button @click="deleteTask">Delete</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>