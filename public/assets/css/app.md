- public/assets/css/app.css is a compiled Tailwind CSS output file, so it is
  expected to appear as a single long line. The build script in this project
  (bin/build-tailwind) takes the real source stylesheet at resources/css/
  input.css, processes Tailwind utilities and your custom styles, then writes
  the result to public/assets/css/app.css in compressed form to reduce file
  size and improve load performance. That means app.css is not meant to be
  edited directly; if you want to change styles, update resources/css/
  input.css (or related source files) and run the build step again so the
  output file is regenerated.

# From the project root (/home/domsvrt/DevelopmentProjects/Projects/sgee-studios), run:

    - ./bin/build-tailwind

    That command compiles resources/css/input.css into public/assets/css/app.css with minification.
    If you get a permission error, run once:

    chmod +x bin/build-tailwind bin/tailwindcss

    then run ./bin/build-tailwind again.
