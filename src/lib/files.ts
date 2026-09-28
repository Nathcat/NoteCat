import fs from "fs";
export const listFilesInDirectory = async (path: string) =>
  new Promise((resolve, reject) =>
    fs.readdir(path, (err, content) => (err ? reject(err) : resolve(content))),
  );
