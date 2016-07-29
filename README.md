# crate-www

[![Build Status](https://travis-ci.org/lotgd/crate-graphql.svg?branch=master)](https://travis-ci.org/lotgd/crate-www)

This is a basic wrapper around the Legend of the Green Dragon [core](https://github.com/lotgd/crate), a so-called crate. It provides a modern interface for clients utilizing GraphQL.

## General GraphQL Schema

While this is not a working schema as graphql describes it, it is a human-readable version of it.

### Types
```
Realm {
  url               string           URL of the realm
  name              string!          Name of the realm
  description       string           Description of the realm
  configuration     Configuration!   Realm configuration
}

Configuration {
  core              Library!         Details about the core
  crate             Library!         Details about the crate
  modules           [Library]        List of details of installed modules
}

Library {
  name              String!          Name of the library
  version           String!          version number, conforming to semantic versioning
  library           String!          Technical name of the library, in vendor/package format
  url               String           Access URL of the library, i.e., location of its code
  author            String           Author, or list of authors, of this library.
}
```

### Queries

- Realm
