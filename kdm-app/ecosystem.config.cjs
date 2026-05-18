module.exports = {
  apps: [
    {
      name: "kdm-reverb",
      script: "artisan",
      interpreter: "php",
      args: "reverb:start",
      instances: 1,
      exec_mode: "fork"
    }
  ]
};