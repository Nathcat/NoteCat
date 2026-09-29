import { getProjectFile, getProjectFileNoContent } from "$lib/files";
import { authenticate_or_redirect } from "$lib/nathcat.net/oauth";
import type { PageServerLoad } from "./$types";
import fs from "fs";

export const load: PageServerLoad = async ({ cookies, params }) => {
  let user = await authenticate_or_redirect(cookies);

  let file = fs
    .readFileSync(getProjectFile(user, params.project, params.filename))
    .toString();

  let project = params.project;
  let filename = params.filename;
  return {
    user,
    project,
    filename,
    file,
  };
};
