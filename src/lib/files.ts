import fs from "fs";
import type { User } from "authcat-oauth-svelte";

export const listFilesInDirectory = async (path: string) =>
  new Promise((resolve, reject) =>
    fs.readdir(path, (err, content) => (err ? reject(err) : resolve(content))),
  );

export const getContentDir = (user: User): string => {
  return "content/" + user.id;
};

export const getProjectDir = (user: User, project: string): string => {
  return getContentDir(user) + "/" + project;
};

export const getProjectFile = (
  user: User,
  project: string,
  file: string,
): string => {
  return getProjectDir(user, project) + "/" + file;
};

export const getProjectRootFile = (user: User, project: string): string => {
  return getProjectFile(user, project, "root.tex");
};

export const getCompileDirNoProject = (): string => {
  return "out";
};

export const getCompileDir = (user: User, project: string): string => {
  return getProjectDir(user, project) + "/" + getCompileDirNoProject();
};

export const getProjectFileNoContent = (
  project: string,
  file: string,
): string => {
  return project + "/" + file;
};

export const getCompiledPdf = (user: User, project: string): string => {
  return getCompileDir(user, project) + "/root.pdf";
};
