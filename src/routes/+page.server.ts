import { listFilesInDirectory } from "$lib/files";
import { authenticate_or_redirect, type User } from "$lib/nathcat.net/oauth";
import type { IEntity } from "@svar-ui/svelte-filemanager";
import type { PageServerLoad } from "./$types";
import fs from "fs";

type Directory = {
  name: string;
  files: IEntity[];
  folders: Directory[];
};

async function map_directory(path: string): Promise<Directory> {
  const content = (await listFilesInDirectory(path)) as string[];

  let dir: Directory = {
    name: path,
    files: [],
    folders: [],
  };

  for (let i = 0; i < content.length; i++) {
    let obj = path + "/" + content[i];
    if (fs.lstatSync(obj).isDirectory())
      dir.folders.push(await map_directory(obj));
    else
      dir.files.push({
        id: obj,
        type: "file",
      });
  }

  return dir;
}

function expand_directory(dir: Directory, arr: IEntity[]): void {
  arr.push({
    id: dir.name,
    type: "folder",
  });

  for (let i = 0; i < dir.files.length; i++) {
    arr.push(dir.files[i]);
  }

  for (let i = 0; i < dir.folders.length; i++) {
    expand_directory(dir.folders[i], arr);
  }
}

export const load: PageServerLoad = async ({ cookies }) => {
  let user: User = await authenticate_or_redirect(cookies);

  let basePath = "content/" + user.id;

  let files: IEntity[] = [];
  let dir = await map_directory(basePath);
  expand_directory(dir, files);

  files = files
    .map((v) => {
      return {
        id: v.id.replace(basePath, "/").replace("//", "/"),
        type: v.type,
      };
    })
    .filter((v) => {
      return v.id !== "/";
    });

  return {
    user,
    files,
  };
};
