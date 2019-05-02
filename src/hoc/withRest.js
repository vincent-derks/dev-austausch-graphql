import React, { PureComponent } from 'react';

import { normalizeRestMenuData } from '../utils';

const getFromRest = async url => {
  try {
    const response = await fetch(process.env.REACT_APP_BACKEND_ROOT + url);
    return await response.json();
  } catch (error) {
    console.warn(error);
    return null;
  }
};

const getSiteSettings = () => getFromRest('/wp-json');
const getPosts = () => getFromRest('/wp-json/wp/v2/posts?per_page=20');
const getMenu = () => getFromRest('/wp-json/menus/v1/menus/2');

export default Comp =>
  class extends PureComponent {
    state = {
      posts: null,
      settings: null
    };
    componentDidMount = async () => {
      const [settings, posts, menu] = await Promise.all([
        getSiteSettings(),
        getPosts(),
        getMenu()
      ]);
      this.setState({
        settings,
        posts,
        menu: normalizeRestMenuData(menu)
      });
    };
    render() {
      return <Comp {...this.props} {...this.state} />;
    }
  };
