import { authenticate_or_redirect } from "$lib/nathcat.net/oauth";
import type { PageServerLoad } from "./$types";
import fs from "fs";

export const load: PageServerLoad = async ({ cookies, params }) => {
  let user = await authenticate_or_redirect(cookies);

  let filePath = "content/" + user.id + "/" + params.filename;
  let file = fs.readFileSync(filePath).toString();

  return {
    user,
    file,
  };
};
