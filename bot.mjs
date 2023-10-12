import fetch from "node-fetch";

// const response = await fetch("https://api-zarena.zinza.com.vn/api/boards/1");
// console.log(response);
async function getBoardData() {
  const response = await fetch("https://api-zarena.zinza.com.vn/api/boards/1");
  const data = await response.json();
  console.log(data);
}
getBoardData();