# FZ Find

A PHP CLI app to find and show entries in your Filezilla's sitemanager.xml file.

Built with [Laravel Zero](https://github.com/laravel-zero/laravel-zero/).

### Usage

Run `./fzfind find "your site name"`

### Making standalone

Run `php fz-find app:build fzfind`

This will add the standalone `phar` file in the _/builds_ folder.

Add this folder to your PATH. E.g., 
`export PATH="/path/to/fz-find/builds:$PATH"`

See the [docs](https://laravel-zero.com/docs/build-a-standalone-application) for more.

