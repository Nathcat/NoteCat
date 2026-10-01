import { logout } from "authcat-oauth-svelte";
import type { PageServerLoad } from "./$types";

export const load: PageServerLoad = async ({ cookies }) => {
  await logout(cookies);
};
