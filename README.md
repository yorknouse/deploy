These are deployment scripts for parts of the Nouse website.
The `Deploy` class has been partly stolen from [Brandon Summers][brandon].

## Usage

Sending a HTTP request to, for example, `http://deploy.nouse.co.uk/nouse.php`, will initiate a `git pull`.
Any changes in the `master` Git branch will then be "deployed".
GitHub [Webhooks][webhooks] should be set up to automatically send a request whenever changes are pushed to GitHub.

`www-data` will need to be able to write to the target directory, and must have read access to the GitHub repository (using a [deploy key][deploykeys] if the repository is private. GitHub requires a different deploy key per repository - see the different keys in `/var/www/.ssh/` and relevant configuration in `/var/www/.ssh/config` and each repository's `.git/config` file).

[brandon]: http://brandonsummers.name/blog/2012/02/10/using-bitbucket-for-automated-deployments/
[webhooks]: https://help.github.com/articles/creating-webhooks/
[deploykeys]: https://developer.github.com/guides/managing-deploy-keys/#deploy-keys

Although these scripts do allow commands to be run after deployments, an alternative is to use [Git hooks][githooks] on the server.
For example, see `.git/hooks/post-merge` in the `nouse` repository, which installs any updated Composer dependencies after a deployment.

[githooks]: http://git-scm.com/book/en/Customizing-Git-Git-Hooks
