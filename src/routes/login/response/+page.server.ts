import { oauth_response_handler } from "$lib/nathcat.net/oauth";
import type { PageServerLoad } from "./$types";

export const load: PageServerLoad = async ({ url, cookies }) => {
  await oauth_response_handler(url, cookies);
};
