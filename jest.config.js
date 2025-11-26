module.exports = {
  testEnvironment: "jsdom",
  setupFilesAfterEnv: ["<rootDir>/tests/js/setupTests.js"],
  testMatch: ["**/tests/js/**/*.test.[jt]s?(x)"],
  transform: {
    "^.+\\.jsx?$": "babel-jest",
  },
};
