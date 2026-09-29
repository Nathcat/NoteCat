<script lang="ts">
  let props: {
    onclick: () => void;
    text: string;
  } = $props();
</script>

<button onclick={props.onclick}>{props.text}</button>

<style>
  button {
    width: 100%;
    height: 100%;
    border-left: none;
    border-right: none;
    border-top: 1px solid var(--quad-color);
    border-bottom: 1px solid var(--quad-color);
    border-radius: 0;
    box-sizing: border-box;
    padding: 10px;
    margin: 0;
  }
</style>
