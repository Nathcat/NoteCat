import { getProjectDir, getProjectRootFile } from "$lib/files";
import { authenticate_or_redirect } from "@kitty-committee/authcat-oauth-svelte";
import type { RequestHandler } from "@sveltejs/kit";
import fs from "fs";

const docTemplate: string =
  "\\documentclass[a4paper, final]{report}\n\n\\begin{document}\nHello world!\n\\end{document}";

export const PUT: RequestHandler = async ({ cookies, params }) => {
  const user = await authenticate_or_redirect(cookies);

  const path = getProjectDir(user, params.project!!);
  const rootFile = getProjectRootFile(user, params.project!!);

  try {
    fs.mkdirSync(path, { recursive: true });
    fs.writeFileSync(rootFile, docTemplate);
  } catch (e: any) {
    console.error(e);
    return new Response(e, { status: 500 });
  }

  return new Response(null, { status: 200 });
};

export const DELETE: RequestHandler = async ({ cookies, params }) => {
  const user = await authenticate_or_redirect(cookies);
  const path = getProjectDir(user, params.project!!);

  if (!fs.existsSync(path)) return new Response(null, { status: 404 });

  try {
    fs.rmSync(path, { recursive: true, force: true });
  } catch (e: any) {
    console.error(e);
    return new Response(e, { status: 500 });
  }

  return new Response(null, { status: 200 });
};
