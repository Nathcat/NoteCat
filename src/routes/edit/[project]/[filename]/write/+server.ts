import { getProjectFile } from "$lib/files";
import { authenticate_or_redirect } from "authcat-oauth-svelte";
import type { RequestHandler } from "@sveltejs/kit";
import fs from "fs";

export const PUT: RequestHandler = async ({ request, cookies, params }) => {
  const user = await authenticate_or_redirect(cookies);
  const file_content = await request.text();
  const target_file_path = getProjectFile(
    user,
    params.project!!,
    params.filename!!,
  );

  fs.writeFileSync(target_file_path, file_content);
  return new Response(null, { status: 200 });
};
