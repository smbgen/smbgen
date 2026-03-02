Problem: native rollup optional dependencies may fail on CI (missing native binary)

Symptoms:
- npm install / npm ci fails on Cloud with error "Cannot find module @rollup/rollup-linux-arm64-gnu" or similar.
- This happens when host `node_modules` (Windows) are mounted into a Linux container, or when optional native packages are not available for the platform.

Root cause:
- Some packages publish platform-specific optional dependencies (native binaries) which npm may try to install or resolve. On certain CI platforms or architectures (ARM64), npm's optional dependency resolution or native extraction can fail.
- Mounting a host `node_modules` directory (Windows) into a Linux container brings incompatible native binaries into the container, which breaks runtime requiring these modules.

Workarounds / Solutions:
1) Preferred (fix for Docker):
- Install node modules inside the container so platform-native binaries are correct for the container OS/arch:

  docker compose run --rm node npm ci --audit=false
  docker compose up --build

- Alternatively, use a named Docker volume for `node_modules` to avoid mounting the host directory:

  services:
    app:
      volumes:
        - ./:/app:delegated
        - node_modules:/app/node_modules

  volumes:
    node_modules:

2) Alternative: add the rollup native optional dependency to `optionalDependencies` in `package.json` so npm is instructed about the platform-specific package. This can help in some environments where npm's optional resolution needs an explicit hint.

3) CI-friendly builds: use `npm ci --audit=false` (or `npm ci --no-optional` where supported) during CI to skip optional native packages and prefer a clean install from lockfile.

Notes:
- Avoid removing lockfiles unless you accept non-reproducible installs across machines.
- If you must mount host `node_modules`, ensure the host and container architectures/OS match, or adopt approach (1).

Workaround applied in this repo:
- Added an `optionalDependencies` entry for `@rollup/rollup-linux-arm64-gnu` in `package.json` as a pragmatic workaround for Cloud builds.
- Also added a `scripts/build_assets.sh` helper for CI builds (already in repo).

If you'd like, I can also add this doc to the main documentation index, or create a short DEVOPS.md with Docker Compose examples.

Basically Windows development deploying to Linux doesn't work too good.