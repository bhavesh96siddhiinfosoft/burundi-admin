const functions = require("firebase-functions");
const admin = require("firebase-admin");

const serviceAccount = require("./serviceAccountKey.json");
admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  databaseURL: "https://burundi-5868d-default-rtdb.firebaseio.com",
});

exports.logUserDataUpdate = functions.firestore
  .document("users/{userId}")
  .onUpdate((change, context) => {
    // Get the before and after data
    const beforeData = change.before.data(); // Data before the update
    const afterData = change.after.data(); // Data after the update

    console.log("User data updated:");
    console.log("Before:", beforeData);
    console.log("After:", afterData);

    return null;
  });
