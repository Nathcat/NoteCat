<script lang="ts">
  import { goto } from "$app/navigation";
  import type { PageProps } from "./$types";
  import {
    Filemanager,
    type IEntity,
    getMenuOptions,
  } from "@svar-ui/svelte-filemanager";
  import { WillowDark } from "@svar-ui/svelte-filemanager";

  let { data }: PageProps = $props();

  const user = data.user;
  const filedata = data.files;

  function menuOptions(mode: any, item: any) {
    switch (mode) {
      default:
        return [
          {
            icon: "wxi-open",
            text: "Open",
            hotkey: "Enter",
            id: "open",
            handler: ({ context }: { context: IEntity }) => {
              goto("/edit" + context.id);
            },
          },
        ];
    }
  }
</script>

<WillowDark>
  <Filemanager data={filedata} {menuOptions}></Filemanager>
</WillowDark>
