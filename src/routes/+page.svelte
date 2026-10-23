<script lang="ts">
  import { goto, invalidateAll } from "$app/navigation";
  import type { PageProps } from "./$types";
  import {
    Filemanager,
    type IEntity,
    type IFileMenuOption,
    type IParsedEntity,
    type TContextMenuType,
    getMenuOptions,
  } from "@svar-ui/svelte-filemanager";
  import { WillowDark } from "@svar-ui/svelte-filemanager";

  let { data }: PageProps = $props();

  const user = $derived(data.user);
  const filedata = $derived(data.files);
  const openFile = (id: string) => {
    goto("/edit" + id);
  };
  function menuOptions(
    mode: TContextMenuType,
    item: IParsedEntity | undefined,
  ): false | IFileMenuOption[] {
    switch (mode) {
      case "add":
        return [
          {
            text: "New project",
            id: "add-folder",
          },
        ];
      case "file":
        return [
          {
            icon: "wxi-open",
            text: "Open",
            hotkey: "Enter",
            id: "open-file",
            handler: ({ context }) => {
              openFile(context.id);
            },
          },
        ];
      case "folder":
        return [
          { icon: "wxi-close", text: "Delete", hotkey: "Delete", id: "delete" },
        ];
      default:
        return [];
    }
  }

  function init(api: any) {
    api.on("open-file", ({ id }: { id: string }) => {
      openFile(id);
    });

    api.intercept(
      "create-file",
      ({ parent, file }: { parent: string; file: File }) => {
        if (file.type !== "folder") return;

        fetch("/manage/" + file.name, {
          method: "PUT",
        })
          .then((r) => {
            if (r.status !== 200) {
              alert("Failed to make project!");
              return r.text();
            } else invalidateAll();
          })
          .then((r) => {
            if (r) console.error(r);
          });
        return false;
      },
    );

    api.intercept("delete-files", ({ ids }: { ids: string[] }) => {
      for (let i = 0; i < ids.length; i++) {
        fetch("/manage" + ids[i], { method: "DELETE" })
          .then((r) => {
            if (r.status !== 200) {
              alert("Failed to delete project!");
              return r.text();
            } else invalidateAll();
          })
          .then((r) => {
            if (r) console.error(r);
          });
      }
    });
  }
</script>

<WillowDark>
  <Filemanager data={filedata} {init} {menuOptions}></Filemanager>
</WillowDark>
