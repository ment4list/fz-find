# FZ Find

A PHP CLI app to find and show entries in your Filezilla's sitemanager.xml file.

Built with [Laravel Zero](https://github.com/laravel-zero/laravel-zero/).

### Usage

Clone the repo and run `./builds/fzfind find "your site name"`.

**Alternatively:**

Add `fzfind` from _/builds/fzfind_ to your path.

Then you can run `./fzfind find "your site name"`

#### Adding to path

Add _/builds_ folder to your PATH. E.g., Add the line to your `.bashrc` or `.zshrc` file.

`export PATH="/path/to/fz-find/builds:$PATH"`

### Building standalone

If you've made changes and want to build the standalone phar, run

`php fz-find app:build fzfind`

This will add the standalone `phar` file in the _/builds_ folder.

See the [docs](https://laravel-zero.com/docs/build-a-standalone-application) for more.
