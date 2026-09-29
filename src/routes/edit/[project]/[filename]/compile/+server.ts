import {
  getCompileDir,
  getCompileDirNoProject,
  getCompiledPdf,
  getProjectDir,
  getProjectFile,
  getProjectRootFile,
} from "$lib/files";
import { authenticate_or_redirect } from "$lib/nathcat.net/oauth";
import type { RequestHandler } from "@sveltejs/kit";
import { execSync } from "child_process";
import fs from "fs";

const fileRegex = new RegExp("^(.*)\\.tex$");

export const GET: RequestHandler = async ({ cookies, params }) => {
  const user = await authenticate_or_redirect(cookies);
  const targetFile = getProjectRootFile(user, params.project!!);

  let match;
  let filename: string;
  if ((match = params.filename!!.match(fileRegex)) !== null) {
    filename = match[1];
  } else return new Response("Invalid file name", { status: 404 });

  const session = cookies.get("session");
  let output: NonSharedBuffer;

  try {
    // Run twice for a full compilation cycle
    execSync(
      'mkdir -p "' +
        getCompileDir(user, params.project!!) +
        '" && cd "' +
        getProjectDir(user, params.project!!) +
        '" && pdflatex -interaction=nonstopmode --output-directory="' +
        getCompileDirNoProject() +
        '" root.tex && pdflatex -interaction=nonstopmode --output-directory="' +
        getCompileDirNoProject() +
        '" root.tex',
    );

    output = fs.readFileSync(getCompiledPdf(user, params.project!!));

    execSync('rm -r "' + getCompileDir(user, params.project!!) + '"');
  } catch (e: any) {
    console.error(e.stdout.toString());
    return new Response(e.stdout.toString(), { status: 500 });
  }

  return new Response(output, {
    status: 200,
    headers: {
      "Content-Type": "application/pdf",
    },
  });
};
