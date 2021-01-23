<script>
  export default {
    methods: {
      $can(role_name) {
        return user_role == role_name ? true : false;
      },
    },
  };
</script>