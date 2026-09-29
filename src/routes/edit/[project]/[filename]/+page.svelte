<script lang="ts">
  import type { PageProps } from "./$types";

  import CodeMirror from "svelte-codemirror-editor";
  import { oneDark } from "@codemirror/theme-one-dark";
  import { latex } from "codemirror-lang-latex";
  import { keymap, type EditorView } from "@codemirror/view";

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

  let previewUrl = $state(data.filename + "/preview");
</script>

<div class="editor-container">
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
          write_state();
          fetch(data.filename + "/compile")
            .then((r) => {
              return r.blob();
            })
            .then((r) => {
              previewUrl = URL.createObjectURL(r);
            });
          return true;
        },
      },
    ]}
  ></CodeMirror>

  <iframe id="preview" src={previewUrl} title="Preview"></iframe>
</div>

<style>
  .editor-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    height: 100%;
  }

  #preview {
    width: 100%;
    height: 100%;
  }
</style>
