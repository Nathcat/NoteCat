<script lang="ts">
  import type { PageProps } from "./$types";
  import { Terminal } from "@xterm/xterm";

  let { data }: PageProps = $props();
  const user = $derived(data.user);
  let value = $derived(data.file);

  let terminal = new Terminal();

  terminal.open(document.getElementById("terminal")!);
  terminal.write("Hello world");
</script>

<div id="terminal"></div>
