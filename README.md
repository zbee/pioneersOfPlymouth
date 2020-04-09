# Pioneers of Plymouth
Pioneers of Plymouth is a Settlers of Catan game clone which was made to
be free, open-source, and server-based.
It was made as existing Settlers of Catan video games are not stable.

There is no license, as I doubt it can be licensed at all. Use and
modify it how you please, you can always contact me from my profile.
I will try to make the setup of your own instance of Pioneers of Plymouth
as simple as possible.

### Issues with running Pioneers of Plymouth out of the box

#### DBAL caching issue

The database abstraction layer in use
[has a caching bug](https://github.com/AdamB7586/pdo-dbal/issues/14)
where query bindValues are saved for multiple queries in the same
script. The work-around for this, to enable POP to work, is after you
have composer install DBAL you need to modify `\DBAL\Database\select()`;
add a line to the beginning of the method like so: `$this->values = [];`.

This will clear the bindValues in the same manner that they're supposed
to be cleared after each query is ran, but for whatever reason is failing.