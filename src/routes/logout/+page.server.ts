import { logout } from "$lib/nathcat.net/oauth";
import type { PageServerLoad } from "./$types";

export const load: PageServerLoad = async ({ cookies }) => {
  logout(cookies);
};
