import React from 'react';
import moment from 'moment';

import { StyledMenu } from './ui';

const Content = ({ settings, posts, menu }) => {
  if (!settings || !posts || !menu) return 'Loading...';
  return (
    <div>
      <h1>{settings.name}</h1>
      <StyledMenu>
        {menu.map(menuItem => {
          return <li key={menuItem.id}>{menuItem.label}</li>;
        })}
      </StyledMenu>
      {posts.map(post => (
        <div key={post.id}>
          <h3>{post.title.rendered}</h3>
          <em>posted on {moment(post.date).format('DD.MM.YYYY HH:MM')}h</em>
          <hr />
        </div>
      ))}
    </div>
  );
};

export default Content;
