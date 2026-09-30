<script lang="ts">
  import type { PageProps } from "./$types";
  import CodeMirror from "svelte-codemirror-editor";
  import { oneDark } from "@codemirror/theme-one-dark";
  import { latex } from "codemirror-lang-latex";
  import { keymap, type EditorView } from "@codemirror/view";
  import { Circle } from "svelte-loading-spinners";
  import { goto } from "$app/navigation";
  import SideButton from "./SideButton.svelte";

  let { data }: PageProps = $props();
  const user = data.user;
  let value = data.file;
  let editor = $state<EditorView>();

  const write_state = () => {
    let text = editor!!.state.doc.toString();
    fetch(data.filename + "/write", {
      method: "PUT",
      body: text,
    });
  };

  const compile = () => {
    write_state();
    enablePreview();

    if (previewUrl !== "") {
      URL.revokeObjectURL(previewUrl);
    }

    previewInit = false;
    loadingPreview = true;
    fetch(data.filename + "/compile")
      .then((r) => {
        return r.blob();
      })
      .then((r) => {
        previewUrl = URL.createObjectURL(r);
        loadingPreview = false;
      });
  };

  let previewUrl = $state("");
  let loadingPreview = $state(false);
  let previewInit = $state(true);
  let showPreview = $state(true);

  const enablePreview = () => {
    showPreview = true;
    gridConfig = "1fr 1fr 0.1fr";
  };

  const disablePreview = () => {
    showPreview = false;
    gridConfig = "1fr 0.1fr";
  };

  const togglePreview = () => {
    if (showPreview) {
      disablePreview();
    } else {
      enablePreview();
    }
  };

  let gridConfig = $state("1fr 1fr 0.1fr");
</script>

<div class="editor-container" style="grid-template-columns: {gridConfig};">
  <CodeMirror
    onready={(cm_view) => (editor = cm_view)}
    onchange={write_state}
    bind:value
    theme={oneDark}
    extensions={[latex()]}
    keybindings={[
      {
        key: "Mod-s",
        run: (_): boolean => {
          compile();
          return true;
        },
      },
    ]}
  ></CodeMirror>

  {#if showPreview}
    {#if previewInit}
      <div id="preview-loading">
        <h3>Hit Ctrl-S while focussed on the editor to compile a preview!</h3>
      </div>
    {:else if !loadingPreview}
      <iframe id="preview" src={previewUrl} title="Preview"></iframe>
    {:else}
      <div id="preview-loading">
        <Circle color="white" size="60" unit="px" />
      </div>
    {/if}
  {/if}

  <div class="column">
    <SideButton onclick={() => goto("/")} text="Home"></SideButton>
    <SideButton onclick={compile} text="Compile preview"></SideButton>
    <SideButton onclick={togglePreview} text="Toggle preview"></SideButton>
  </div>
</div>

<style>
  .editor-container {
    display: grid;
    height: 100%;
  }

  #preview {
    width: 100%;
    height: 100%;
  }

  #preview-loading {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
  }

  #preview-loading h3 {
    text-align: center;
    margin: 10%;
  }
</style>
