module.exports = {
  testEnvironment: 'node',
  setupFilesAfterEnv: ['./tests/setup.js'],
  testTimeout: 600000 // 10 minutes timeout for mongodb-memory-server initial download
};
