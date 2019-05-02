export const normalizeGraphQlData = rawData => ({
  settings: {
    name: rawData.generalSettings.title
  },
  posts: rawData.posts.edges.map(edge => {
    return {
      ...edge.node,
      title: {
        rendered: edge.node.title
      }
    };
  }),
  menu: rawData.menuItems.edges.map(menuItem => ({ ...menuItem.node }))
});

export const normalizeRestMenuData = rawData =>
  rawData.map(menuItem => ({
    id: menuItem.ID,
    label: menuItem.title
  }));
