<script lang="ts">
  import type { PageProps } from "./$types";

  import CodeMirror from "svelte-codemirror-editor";
  import { oneDark } from "@codemirror/theme-one-dark";
  import { latex } from "codemirror-lang-latex";

  let { data }: PageProps = $props();
  const user = data.user;
  let value = data.file;
</script>

<div class="editor-container">
  <CodeMirror bind:value theme={oneDark} extensions={[latex()]}></CodeMirror>
</div>

<style>
  .editor-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
  }
</style>
