import { listFilesInDirectory } from "$lib/files";
import { authenticate_or_redirect, type User } from "$lib/nathcat.net/oauth";
import type { IEntity } from "@svar-ui/svelte-filemanager";
import type { PageServerLoad } from "./$types";
import fs from "fs";

export const load: PageServerLoad = async ({ cookies }) => {
  let user: User = await authenticate_or_redirect(cookies);

  let basePath = "content/" + user.id + "/";

  let files: IEntity[] = (
    (await listFilesInDirectory(basePath)) as string[]
  ).map((v) => {
    return {
      id: "/" + v,
      type: fs.lstatSync(basePath + v).isDirectory() ? "folder" : "file",
    };
  });

  return {
    user,
    files,
  };
};
