import React, { PureComponent } from 'react';
import ApolloClient from 'apollo-boost';
import { ApolloProvider, Query } from 'react-apollo';
import gql from 'graphql-tag';

import { normalizeGraphQlData } from '../utils';

const query = gql`
  query($location: MenuLocationEnum, $postCount: Int) {
    generalSettings {
      title
    }
    posts(first: $postCount) {
      edges {
        node {
          id
          date
          title
        }
      }
    }
    menuItems(where: { location: $location }) {
      edges {
        node {
          id
          label
        }
      }
    }
  }
`;

export default Comp =>
  class extends PureComponent {
    render() {
      const client = new ApolloClient({
        uri: 'http://localhost:8000/graphql',
        fetchOptions: {
          headers: {}
        }
      });
      return (
        <ApolloProvider client={client}>
          <Query
            query={query}
            variables={{ location: 'MENU_1', postCount: 20 }}
          >
            {({ data, loading }) => {
              const state = loading ? null : normalizeGraphQlData(data);
              return <Comp {...this.props} {...state} />;
            }}
          </Query>
        </ApolloProvider>
      );
    }
  };
