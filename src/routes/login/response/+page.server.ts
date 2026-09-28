import { oauth_response_handler } from "$lib/nathcat.net/oauth";
import type { PageServerLoad } from "./$types";

export const load: PageServerLoad = oauth_response_handler;
